<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSitePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminPermission('pages.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-z][a-z0-9-]*$/', Rule::notIn(['about', 'vision', 'gallery', 'admin', 'products', 'locations', 'contact', 'language']), 'unique:site_pages,key'],
            'title' => ['required', 'array:ar,en'],
            'title.ar' => ['required', 'string', 'max:255'],
            'title.en' => ['required', 'string', 'max:255'],
            'body' => ['required', 'array:ar,en'],
            'body.ar' => ['required', 'string', 'max:20000'],
            'body.en' => ['required', 'string', 'max:20000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
