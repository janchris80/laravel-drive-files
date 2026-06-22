<?php

namespace Janchris80\DriveFiles\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUploadSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxBytes = (int) (config('drive-files.max_file_size_mb', 100) * 1024 * 1024);
        $allowed  = config('drive-files.allowed_mime_types', []);

        return [
            'filename'         => ['required', 'string', 'max:255'],
            'mime_type'        => ['required', 'string', Rule::in($allowed)],
            'size_bytes'       => ['required', 'integer', 'min:1', 'max:'.$maxBytes],
            'origin'           => ['required', 'url'],
            'parent_folder_id' => ['nullable', 'string', 'max:255'],
            'category'         => ['nullable', 'string', 'max:64'],
            'office_id'        => ['nullable', 'integer'],
        ];
    }
}
