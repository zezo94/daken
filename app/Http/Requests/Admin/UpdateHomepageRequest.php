<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomepageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminPermission('homepage.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'hero_title_ar' => ['required', 'string', 'max:150'],
            'hero_title_en' => ['required', 'string', 'max:150'],

            'hero_subtitle_ar' => ['required', 'string', 'max:500'],
            'hero_subtitle_en' => ['required', 'string', 'max:500'],

            'hero_button_text_ar' => ['required', 'string', 'max:60'],
            'hero_button_text_en' => ['required', 'string', 'max:60'],

            'hero_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ];
    }
}
