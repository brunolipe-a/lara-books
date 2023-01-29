<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request)
    {
        $data = $this->data();

        return [
            'id' => $this->id(),
            'name' => $data['name'],
            'category' => $data['category'],
            'author' => $data['author'],
            'year' => $data['year'],
            'number_of_pages' => $data['number_of_pages'],
            'language' => $data['language'],
            'edition' => $data['edition'],
            'publisher' => [
                'name' => $data['publisher']['name'],
                'code' => $data['publisher']['code'],
                'phone' => $data['publisher']['phone'],
            ],
            'isbn' => $data['isbn'],
        ];
    }
}
