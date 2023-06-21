<?php

namespace App\Http\Requests;

class RegisterUserRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ];
    }
}