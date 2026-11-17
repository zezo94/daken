<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminPermission('products.manage') ?? false;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id'),
            ],
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.ar' => ['nullable', 'string', 'max:5000'],
            'description.en' => ['nullable', 'string', 'max:5000'],
            'slug' => [
                'required',
                'alpha_dash:ascii',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product),
            ],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product),
            ],
            'weight' => [
                'nullable',
                'numeric',
                'min:0',
                'decimal:0,2',
                'max:999999.99',
            ],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_active' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:4294967295',
            ],
        ];
    }
}
