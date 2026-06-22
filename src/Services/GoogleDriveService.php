<?php

namespace Janchris80\DriveFiles\Services;

use GuzzleHttp\Client as GuzzleClient;
use Janchris80\DriveFiles\Contracts\DriveStorageInterface;
use RuntimeException;
use Throwable;

class GoogleDriveService implements DriveStorageInterface
{
    protected ?\Google\Client $client = null;
    protected ?\Google\Service\Drive $drive = null;

    public function __construct(protected array $config)
    {
    }

    protected function getClient(): \Google\Client
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $client = new \Google\Client();
        $client->setApplicationName($this->config['application_name'] ?? 'Laravel Drive Files');
        $client->setScopes([\Google\Service\Drive::DRIVE]);

        if (! empty($this->config['client_id']) && ! empty($this->config['refresh_token'])) {
            $client->setClientId($this->config['client_id']);
            $client->setClientSecret($this->config['client_secret']);
            $client->setRefreshToken($this->config['refresh_token']);
        } elseif (! empty($this->config['credentials_path'])) {
            $path = $this->config['credentials_path'];
            if (! \Illuminate\Support\Str::startsWith($path, '/') && ! \Illuminate\Support\Str::contains($path, ':')) {
                $path = base_path($path);
            }
            $client->setAuthConfig($path);
        }

        $client->setHttpClient(new GuzzleClient([
            'timeout'         => 30,
            'connect_timeout' => 10,
        ]));

        return $this->client = $client;
    }

    protected function getDrive(): \Google\Service\Drive
    {
        return $this->drive ??= new \Google\Service\Drive($this->getClient());
    }

    /**
     * Initialize a resumable upload session. Returns the Google-provided upload URI
     * which the browser uses to PUT bytes directly to Drive.
     *
     * Expected $params keys: name, mime_type, size_bytes, origin, parent_folder_id
     */
    public function createResumableSession(array $params): array
    {
        try {
            $metadata = [
                'name'     => $params['name'],
                'mimeType' => $params['mime_type'],
            ];

            if (! empty($params['parent_folder_id'])) {
                $metadata['parents'] = [$params['parent_folder_id']];
            }

            $client = $this->getClient();
            $token = $client->getAccessToken();
            if (! $token || empty($token['access_token'])) {
                $client->fetchAccessTokenWithRefreshToken();
                $token = $client->getAccessToken();
            }

            $uploadUrl = 'https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable&supportsAllDrives=true';

            $sharedDriveId = $this->config['shared_drive_id'] ?? null;
            if ($sharedDriveId) {
                $uploadUrl .= '&corpora=drive&driveId=' . $sharedDriveId;
            }

            $http = new GuzzleClient(['timeout' => 30]);
            $response = $http->post(
                $uploadUrl,
                [
                    'headers' => [
                        'Authorization'             => 'Bearer '.($token['access_token'] ?? ''),
                        'Content-Type'              => 'application/json; charset=UTF-8',
                        'X-Upload-Content-Type'     => $params['mime_type'],
                        'X-Upload-Content-Length'   => (string) $params['size_bytes'],
                        'Origin'                    => $params['origin'],
                    ],
                    'body' => json_encode($metadata),
                ]
            );

            $location = $response->getHeader('Location')[0] ?? null;

            if (! $location) {
                throw new RuntimeException('Google Drive did not return an upload URI.');
            }

            return ['upload_uri' => $location];
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to create resumable session: '.$e->getMessage(), 0, $e);
        }
    }

    public function getFileInfo(string $id): array
    {
        try {
            $file = $this->getDrive()->files->get($id, [
                'fields'            => 'id,name,mimeType,size,webViewLink,webContentLink,parents,createdTime',
                'supportsAllDrives' => true,
            ]);

            return [
                'id'              => $file->getId(),
                'name'            => $file->getName(),
                'mimeType'        => $file->getMimeType(),
                'size'            => $file->getSize(),
                'webViewLink'     => $file->getWebViewLink(),
                'webContentLink'  => $file->getWebContentLink(),
                'parents'         => $file->getParents() ?? [],
                'createdTime'     => $file->getCreatedTime(),
            ];
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to fetch file info: '.$e->getMessage(), 0, $e);
        }
    }

    public function deleteFile(string $id): bool
    {
        try {
            $this->getDrive()->files->delete($id, ['supportsAllDrives' => true]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function createPublicPermission(string $id): string
    {
        try {
            $perm = new \Google\Service\Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader',
            ]);

            $this->getDrive()->permissions->create($id, $perm, [
                'supportsAllDrives' => true,
            ]);

            $info = $this->getFileInfo($id);
            return $info['webViewLink'] ?? $info['webContentLink'] ?? '';
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to create public permission: '.$e->getMessage(), 0, $e);
        }
    }

    public function removePublicPermission(string $id): bool
    {
        try {
            $perms = $this->getDrive()->permissions->listPermissions($id, [
                'supportsAllDrives' => true,
                'fields'            => 'permissions(id,type)',
            ]);

            foreach ($perms->getPermissions() as $p) {
                if ($p->getType() === 'anyone') {
                    $this->getDrive()->permissions->delete($id, $p->getId(), [
                        'supportsAllDrives' => true,
                    ]);
                }
            }

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * NOTE: Google Drive does not natively support signed URLs.
     * For real temporary URLs, proxy downloads through your app using
     * the OAuth access token. This default returns the webContentLink.
     */
    public function getTemporaryUrl(string $id, int $ttl = 3600): string
    {
        $info = $this->getFileInfo($id);
        return $info['webContentLink'] ?? $info['webViewLink'] ?? '';
    }

    public function getPreviewUrl(string $id): string
    {
        $info = $this->getFileInfo($id);
        return $info['webViewLink'] ?? '';
    }
}
