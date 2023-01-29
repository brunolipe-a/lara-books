<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReaderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['sometimes'],
            'email' => ['sometimes', 'email'],
            'password' => ['sometimes'],
            'birthday' => ['sometimes', 'date_format:Y-m-d'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
