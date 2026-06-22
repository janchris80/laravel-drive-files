<?php

namespace Janchris80\DriveFiles\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListDriveFilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'     => ['nullable', 'string', 'max:191'],
            'mime_type'  => ['nullable', 'string', 'max:191'],
            'category'   => ['nullable', 'string', 'max:64'],
            'visibility' => ['nullable', 'in:public,private'],
            'sort'       => ['nullable', 'in:name,size_bytes,created_at,mime_type'],
            'direction'  => ['nullable', 'in:asc,desc'],
            'per_page'   => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
