<?php

namespace Janchris80\DriveFiles\Services;

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Janchris80\DriveFiles\Contracts\DriveStorageInterface;
use Janchris80\DriveFiles\Models\DriveToken;
use RuntimeException;
use Throwable;

class GoogleDriveService implements DriveStorageInterface
{
    protected ?\Google\Client $client = null;
    protected ?\Google\Service\Drive $drive = null;

    public function __construct(protected array $config)
    {
    }

    /**
     * Build a fresh OAuth client (no token attached). Used for the
     * authorize URL and code exchange flow.
     */
    public function buildClient(): \Google\Client
    {
        $oauth = $this->config['oauth'] ?? [];

        $client = new \Google\Client();
        $client->setApplicationName($this->config['application_name'] ?? 'Laravel Drive Files');
        $client->setClientId($oauth['client_id'] ?? '');
        $client->setClientSecret($oauth['client_secret'] ?? '');
        $client->setRedirectUri($oauth['redirect_uri'] ?? '');
        $client->setScopes($oauth['scopes'] ?? ['https://www.googleapis.com/auth/drive.file']);
        $client->setAccessType($oauth['access_type'] ?? 'offline');
        $client->setPrompt($oauth['prompt'] ?? 'consent');
        $client->setIncludeGrantedScopes(true);

        $client->setHttpClient(new GuzzleClient([
            'timeout'         => 30,
            'connect_timeout' => 10,
        ]));

        return $client;
    }

    public function getAuthUrl(): string
    {
        return $this->buildClient()->createAuthUrl();
    }

    public function getToken(): ?DriveToken
    {
        return DriveToken::current();
    }

    /**
     * Exchange an authorization code for tokens and persist the
     * singleton DriveToken row.
     */
    public function handleCallback(string $code): DriveToken
    {
        $client = $this->buildClient();
        $data   = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($data['error'])) {
            throw new RuntimeException(
                'OAuth error: ' . ($data['error_description'] ?? $data['error'])
            );
        }

        $expiresAt = Carbon::now()->addSeconds((int) ($data['expires_in'] ?? 3600));

        // Best-effort: capture the email of the connected Google account
        // so it's visible in `drive:status` and the web /oauth/status endpoint.
        $email = null;
        try {
            $client->setAccessToken($data);
            $oauth2 = new \Google\Service\Oauth2($client);
            $email  = $oauth2->userinfo->get()->getEmail();
        } catch (Throwable $e) {
            // userinfo.email scope may not have been granted; silently ignore.
        }

        $attributes = [
            'access_token'    => $data['access_token']    ?? '',
            'expires_at'      => $expiresAt,
            'scope'           => $data['scope']           ?? null,
            'token_type'      => $data['token_type']      ?? 'Bearer',
            'connected_email' => $email,
        ];

        // Google only returns refresh_token on the FIRST consent unless
        // prompt=consent is forced. Don't wipe an existing one.
        if (! empty($data['refresh_token'])) {
            $attributes['refresh_token'] = $data['refresh_token'];
        }

        $token = DriveToken::current() ?? new DriveToken();
        $token->fill($attributes);
        $token->save();

        return $token;
    }

    public function disconnect(): bool
    {
        return DriveToken::query()->delete() > 0;
    }

    /**
     * Return an authenticated client for the singleton token,
     * refreshing the access token if it has expired.
     */
    protected function getClient(): \Google\Client
    {
        $token = DriveToken::current();

        if (! $token) {
            throw new RuntimeException(
                'Google Drive is not connected. Run `php artisan drive:connect` first, '
                . 'or visit GET /api/v1/drive/oauth/redirect as an admin.'
            );
        }

        if ($this->client !== null) {
            return $this->client;
        }

        $client = $this->buildClient();
        $client->setAccessToken($token->toGoogleArray());

        if ($client->isAccessTokenExpired()) {
            if (empty($token->refresh_token)) {
                throw new RuntimeException(
                    'Access token expired and no refresh token is stored. '
                    . 'Reconnect via `php artisan drive:connect`.'
                );
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken($token->refresh_token);

            if (isset($newToken['error'])) {
                throw new RuntimeException(
                    'Token refresh failed: ' . ($newToken['error_description'] ?? $newToken['error'])
                );
            }

            $token->update([
                'access_token' => $newToken['access_token'] ?? $token->access_token,
                'expires_at'   => Carbon::now()->addSeconds((int) ($newToken['expires_in'] ?? 3600)),
                'scope'        => $newToken['scope']        ?? $token->scope,
                'token_type'   => $newToken['token_type']   ?? $token->token_type,
            ]);

            $client->setAccessToken($token->fresh()->toGoogleArray());
        }

        return $this->client = $client;
    }

    protected function getDrive(): \Google\Service\Drive
    {
        return $this->drive ??= new \Google\Service\Drive($this->getClient());
    }

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
            $token  = $client->getAccessToken();

            $http = new GuzzleClient(['timeout' => 30]);
            $response = $http->post(
                'https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable',
                [
                    'headers' => [
                        'Authorization'           => 'Bearer ' . ($token['access_token'] ?? ''),
                        'Content-Type'            => 'application/json; charset=UTF-8',
                        'X-Upload-Content-Type'   => $params['mime_type'],
                        'X-Upload-Content-Length' => (string) $params['size_bytes'],
                        'Origin'                  => $params['origin'],
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
            throw new RuntimeException('Failed to create resumable session: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getFileInfo(string $id): array
    {
        try {
            $file = $this->getDrive()->files->get($id, [
                'fields' => 'id,name,mimeType,size,webViewLink,webContentLink,parents,createdTime',
            ]);

            return [
                'id'             => $file->getId(),
                'name'           => $file->getName(),
                'mimeType'       => $file->getMimeType(),
                'size'           => $file->getSize(),
                'webViewLink'    => $file->getWebViewLink(),
                'webContentLink' => $file->getWebContentLink(),
                'parents'        => $file->getParents() ?? [],
                'createdTime'    => $file->getCreatedTime(),
            ];
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to fetch file info: ' . $e->getMessage(), 0, $e);
        }
    }

    public function deleteFile(string $id): bool
    {
        try {
            $this->getDrive()->files->delete($id);
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

            $this->getDrive()->permissions->create($id, $perm);

            $info = $this->getFileInfo($id);
            return $info['webViewLink'] ?? $info['webContentLink'] ?? '';
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to create public permission: ' . $e->getMessage(), 0, $e);
        }
    }

    public function removePublicPermission(string $id): bool
    {
        try {
            $perms = $this->getDrive()->permissions->listPermissions($id, [
                'fields' => 'permissions(id,type)',
            ]);

            foreach ($perms->getPermissions() as $p) {
                if ($p->getType() === 'anyone') {
                    $this->getDrive()->permissions->delete($id, $p->getId());
                }
            }

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

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
