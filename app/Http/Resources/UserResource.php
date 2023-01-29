<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $data = $this->data();

        return [
            'id' => $this->id(),
            'name' => $data['name'],
            'email' => $data['email'],
            'birthday' => $data['birthday'],
            'is_active' => $data['is_active'],
        ];
    }
}
