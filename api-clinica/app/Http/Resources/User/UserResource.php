<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "surname" => $this->resource->surname,
            "email" => $this->resource->email,
            "birth_date" => $this->resource->birth_date,
            "gender" => $this->resource->gender,
            "education" => $this->resource->education,
            "designation" => $this->resource->designation,
            "address" => $this->resource->address,
            "role" => $this->resource->roles->first(),
            "avatar" => env("APP_URL")."storage/".$this->resoruce->avatar, 
        ];
    }
}
