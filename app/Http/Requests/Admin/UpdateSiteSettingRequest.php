<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateSiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminPermission('settings.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'brand_name' => ['required', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],

            'instagram_url' => ['nullable', 'url:http,https', 'max:2048'],
            'facebook_url' => ['nullable', 'url:http,https', 'max:2048'],
            'tiktok_url' => ['nullable', 'url:http,https', 'max:2048'],

            'logo' => [
                'nullable',
                'image',
                'mimes:png,jpg,jpeg,webp',
                'max:2048',
            ],

            'favicon' => [
                'nullable',
                'file',
                'mimes:png,webp',
                'max:512',
                Rule::prohibitedIf(
                    fn () => $this->boolean('reset_favicon')
                ),
            ],

            'reset_favicon' => ['sometimes', 'boolean'],
        ];
    }
}
