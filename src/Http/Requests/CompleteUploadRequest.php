<?php

namespace Janchris80\DriveFiles\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'google_file_id' => ['required', 'string', 'max:255'],
            'category'       => ['nullable', 'string', 'max:64'],
            'meta'           => ['nullable', 'array'],
        ];
    }
}
