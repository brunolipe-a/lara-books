<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['sometimes'],
            'category' => ['sometimes'],
            'author' => ['sometimes'],
            'year' => ['sometimes'],
            'number_of_pages' => ['sometimes'],
            'language' => ['sometimes'],
            'edition' => ['sometimes'],
            'publisher' => ['sometimes', 'array'],
            'publisher.name' => ['sometimes'],
            'publisher.code' => ['sometimes'],
            'publisher.phone' => ['sometimes'],
            'isbn' => ['sometimes'],
        ];
    }
}
