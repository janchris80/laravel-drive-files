<?php

namespace Janchris80\DriveFiles\Actions;

use Janchris80\DriveFiles\Events\DriveFileUploaded;
use Janchris80\DriveFiles\Models\DriveFile;
use Janchris80\DriveFiles\Services\GoogleDriveService;

class CompleteUploadAction
{
    public function __construct(private readonly GoogleDriveService $drive)
    {
    }

    public function execute(array $validated, $user = null): DriveFile
    {
        $info = $this->drive->getFileInfo($validated['google_file_id']);

        $model = DriveFile::create([
            'name'                   => $info['name'] ?? 'Untitled',
            'google_file_id'         => $info['id'] ?? $validated['google_file_id'],
            'google_drive_folder_id' => $info['parents'][0] ?? null,
            'mime_type'              => $info['mimeType'] ?? null,
            'size_bytes'             => (int) ($info['size'] ?? 0),
            'visibility'             => 'private',
            'category'               => $validated['category'] ?? null,
            'office_id'              => $validated['office_id'] ?? null,
            'uploaded_by_user_id'    => $user?->id,
            'meta'                   => $validated['meta'] ?? null,
        ]);

        event(new DriveFileUploaded($model));

        return $model;
    }
}
