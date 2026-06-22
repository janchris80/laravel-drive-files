<?php

namespace Janchris80\DriveFiles\Actions;

use Janchris80\DriveFiles\Events\DriveFileShared;
use Janchris80\DriveFiles\Models\DriveFile;
use Janchris80\DriveFiles\Services\GoogleDriveService;
use RuntimeException;

class ShareDriveFileAction
{
    public function __construct(private readonly GoogleDriveService $drive)
    {
    }

    public function execute(DriveFile $file, $user): DriveFile
    {
        if (! config('drive-files.public_links_enabled', false)) {
            throw new RuntimeException('Public links are disabled in drive-files config.');
        }

        $link = $this->drive->forUser($user)->createPublicPermission($file->google_file_id);

        $file->update([
            'public_link' => $link,
            'visibility'  => 'public',
        ]);

        event(new DriveFileShared($file));

        return $file->fresh();
    }
}
