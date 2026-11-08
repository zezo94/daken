<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminPermission('categories.manage') ?? false;
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => ['required', 'array'],

            'name.ar' => [
                'required',
                'string',
                'max:255',
            ],

            'name.en' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'alpha_dash:ascii',
                'max:255',
                Rule::unique('categories', 'slug')
                    ->ignore($category),
            ],

            'description' => [
                'nullable',
                'array',
            ],

            'description.ar' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'description.en' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}
