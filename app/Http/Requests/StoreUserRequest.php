<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
            'birthday' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
