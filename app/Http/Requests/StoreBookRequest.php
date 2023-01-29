<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'category' => ['required'],
            'author' => ['required'],
            'year' => ['required'],
            'number_of_pages' => ['required', 'integer'],
            'language' => ['required'],
            'edition' => ['required'],
            'publisher' => ['required', 'array'],
            'publisher.name' => ['required'],
            'publisher.code' => ['required'],
            'publisher.phone' => ['required'],
            'isbn' => ['required'],
        ];
    }
}
