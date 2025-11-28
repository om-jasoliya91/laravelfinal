<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class CreateUser extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{
    return [
        'firstname' => $this->firstname,
        'lastname' => $this->lastname,
        'dob' => $this->dob,
        'gender' => $this->gender,
        'phone' => $this->phone,
        //error only i used "," instead of "."
        'profile' => asset('storage/'. $this->profile) ,
        'email' => $this->email,
        'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        'updated_at' => $this->created_at->format('Y-m-d H:i:s')
    ];
}

}