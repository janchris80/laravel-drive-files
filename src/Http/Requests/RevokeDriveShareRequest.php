<?php

namespace Janchris80\DriveFiles\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevokeDriveShareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
