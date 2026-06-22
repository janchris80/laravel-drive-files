<?php

namespace Janchris80\DriveFiles\Actions;

use Janchris80\DriveFiles\Services\GoogleDriveService;

class CreateUploadSessionAction
{
    public function __construct(private readonly GoogleDriveService $drive)
    {
    }

    public function execute(array $validated, $user = null): array
    {
        return $this->drive->createResumableSession([
            'name'             => $validated['filename'],
            'mime_type'        => $validated['mime_type'],
            'size_bytes'       => $validated['size_bytes'],
            'origin'           => $validated['origin'],
            'parent_folder_id' => $validated['parent_folder_id']
                ?? config('drive-files.root_folder_id'),
        ]);
    }
}
