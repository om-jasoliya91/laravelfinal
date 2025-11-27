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
        'profile' => $this->profile ? url('/storage', $this->profile_pic) : null, // Fixed reference
        'email' => $this->email,
    ];
}

}