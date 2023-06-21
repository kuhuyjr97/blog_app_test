<?php

namespace App\Http\Requests;

class LoginUserRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'email' => 'required',
            'password' => 'required',
        ];
    }
}
