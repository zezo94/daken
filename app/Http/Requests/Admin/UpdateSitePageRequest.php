<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSitePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminPermission('pages.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array:ar,en'],
            'title.ar' => ['required', 'string', 'max:255'],
            'title.en' => ['required', 'string', 'max:255'],

            'body' => ['required', 'array:ar,en'],
            'body.ar' => ['required', 'string', 'max:20000'],
            'body.en' => ['required', 'string', 'max:20000'],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'remove_image' => ['nullable', 'boolean'],

            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الصفحة مطلوب.',
            'title.ar.required' => 'عنوان الصفحة بالعربية مطلوب.',
            'title.en.required' => 'عنوان الصفحة بالإنجليزية مطلوب.',
            'title.ar.max' => 'عنوان الصفحة بالعربية يجب ألا يتجاوز 255 حرفًا.',
            'title.en.max' => 'عنوان الصفحة بالإنجليزية يجب ألا يتجاوز 255 حرفًا.',

            'body.required' => 'محتوى الصفحة مطلوب.',
            'body.ar.required' => 'محتوى الصفحة بالعربية مطلوب.',
            'body.en.required' => 'محتوى الصفحة بالإنجليزية مطلوب.',
            'body.ar.max' => 'المحتوى بالعربية يجب ألا يتجاوز 20000 حرف.',
            'body.en.max' => 'المحتوى بالإنجليزية يجب ألا يتجاوز 20000 حرف.',

            'image.image' => 'الملف المرفوع يجب أن يكون صورة.',
            'image.mimes' => 'الصورة يجب أن تكون بصيغة JPG أو PNG أو WebP.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',

            'remove_image.boolean' => 'قيمة حذف الصورة غير صحيحة.',
            'is_active.required' => 'يرجى تحديد حالة نشر الصفحة.',
            'is_active.boolean' => 'حالة نشر الصفحة غير صحيحة.',
        ];
    }
}
