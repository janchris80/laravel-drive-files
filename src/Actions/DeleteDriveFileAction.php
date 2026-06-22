<?php

namespace Janchris80\DriveFiles\Actions;

use Janchris80\DriveFiles\Events\DriveFileDeleted;
use Janchris80\DriveFiles\Models\DriveFile;
use Janchris80\DriveFiles\Services\GoogleDriveService;

class DeleteDriveFileAction
{
    public function __construct(private readonly GoogleDriveService $drive)
    {
    }

    public function execute(DriveFile $file, $user): void
    {
        $this->drive->forUser($user)->deleteFile($file->google_file_id);
        $file->delete();
        event(new DriveFileDeleted($file));
    }
}
