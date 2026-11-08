<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminPermission('locations.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            // اسم المكان بالعربية والإنجليزية
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],

            // عنوان المكان
            'address' => ['nullable', 'array'],
            'address.ar' => ['nullable', 'string', 'max:500'],
            'address.en' => ['nullable', 'string', 'max:500'],

            // المدينة والدولة نصوص عادية
            'city' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],

            // معلومات الاتصال
            'phone' => ['nullable', 'string', 'max:30'],

            // إحداثيات الموقع
            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            // صورة أو شعار المكان
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            // رابط خرائط Google
            'google_maps_url' => [
                'nullable',
                'url:http,https',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    $host = strtolower(
                        parse_url($value, PHP_URL_HOST) ?? ''
                    );

                    $allowedHosts = [
                        'google.com',
                        'www.google.com',
                        'maps.google.com',
                        'maps.app.goo.gl',
                    ];

                    if (!in_array($host, $allowedHosts, true)) {
                        $fail('يجب إدخال رابط Google Maps صحيح.');
                    }
                },
            ],

            // حالة المكان وترتيبه
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.ar.required' => 'اسم المكان بالعربية مطلوب.',
            'name.en.required' => 'اسم المكان بالإنجليزية مطلوب.',

            'city.required' => 'يرجى إدخال المدينة.',
            'country.required' => 'يرجى إدخال الدولة.',

            'image.image' => 'الملف المختار يجب أن يكون صورة.',
            'image.mimes' => 'الصورة يجب أن تكون JPG أو PNG أو WebP.',
            'image.max' => 'الحد الأقصى لحجم الصورة هو 2 ميجابايت.',

            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و90.',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و180.',

            'sort_order.required' => 'ترتيب العرض مطلوب.',
            'sort_order.min' => 'ترتيب العرض لا يمكن أن يكون سالبًا.',
        ];
    }
}
