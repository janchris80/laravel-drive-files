<?php

namespace Janchris80\DriveFiles\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriveFileListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'mime_type'  => $this->mime_type,
            'size_bytes' => $this->size_bytes,
            'size_kb'    => $this->size_kb,
            'visibility' => $this->visibility,
            'category'   => $this->category,
            'created_at' => $this->created_at,
        ];
    }
}
