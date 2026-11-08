<?php

namespace App\Http\Requests\Admin;

class UpdateLocationRequest extends StoreLocationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'remove_image' => ['nullable', 'boolean'],
        ]);
    }
}
