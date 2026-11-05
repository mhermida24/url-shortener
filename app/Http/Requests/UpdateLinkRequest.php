<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'original_url' => ['required', 'url:http,https', 'max:2048'],
            'expires_at'   => ['nullable', 'date'],
            'max_clicks'   => ['nullable', 'integer', 'min:1'],
        ];
    }
}