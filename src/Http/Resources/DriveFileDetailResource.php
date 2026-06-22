<?php

namespace Janchris80\DriveFiles\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriveFileDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->name,
            'google_file_id'         => $this->google_file_id,
            'google_drive_folder_id' => $this->google_drive_folder_id,
            'mime_type'              => $this->mime_type,
            'size_bytes'             => $this->size_bytes,
            'size_kb'                => $this->size_kb,
            'visibility'             => $this->visibility,
            'public_link'            => $this->public_link,
            'category'               => $this->category,
            'meta'                   => $this->meta,
            'office_id'              => $this->office_id,
            'uploaded_by_user_id'    => $this->uploaded_by_user_id,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
            'deleted_at'             => $this->deleted_at,
        ];
    }
}
