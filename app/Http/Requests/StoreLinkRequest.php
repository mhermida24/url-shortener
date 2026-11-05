<?php

namespace App\Http\Requests;

use App\Models\Link;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'original_url' => ['required', 'url:http,https', 'max:2048'],
            'code' => [
                'nullable',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z0-9_-]+$/',
                'unique:links,code',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (in_array(strtolower($value), Link::RESERVED_CODES, true)) {
                        $fail('Ese código está reservado, elegí otro.');
                    }
                },
            ],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'max_clicks' => ['nullable', 'integer', 'min:1'],
        ];
    }
}