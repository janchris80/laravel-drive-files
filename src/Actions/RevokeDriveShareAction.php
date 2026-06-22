<?php

namespace Janchris80\DriveFiles\Actions;

use Janchris80\DriveFiles\Events\DriveFileShareRevoked;
use Janchris80\DriveFiles\Models\DriveFile;
use Janchris80\DriveFiles\Services\GoogleDriveService;

class RevokeDriveShareAction
{
    public function __construct(private readonly GoogleDriveService $drive)
    {
    }

    public function execute(DriveFile $file, $user): DriveFile
    {
        $this->drive->forUser($user)->removePublicPermission($file->google_file_id);

        $file->update([
            'public_link' => null,
            'visibility'  => 'private',
        ]);

        event(new DriveFileShareRevoked($file));

        return $file->fresh();
    }
}
