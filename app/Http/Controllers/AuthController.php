<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    
    // create function register return $fields validate request
    public function register(RegisterUserRequest $request): JsonResponse
{        
    // check email exists
    if (User::whereEmail($request->email)->exists()) {
        return response()->json(['errors' => ['email' => ['The email has already been taken.']]], 422);
    }   

    // create user from request
    $user = User::create([
        'name' => $request->name,
        'email' => $request['email'] ,
        'password' => bcrypt($request['password'])
    ]);

    // create message if fail to create user
    if (!$user) {
        return response()->json([
            'message' => 'Registration failed'
        ], 422);
    }

    // create token from user
    $token = $user->createToken('myapptoken')->plainTextToken;

    // create response from user and token
    $response = [
        'user' => $user, 
        'token' => $token
    ];

    return response()->json($response, 201);
}


    //create log in function
    public function login(LoginUserRequest $request){
        
        //check email 
        $user = User::where('email' , $request['email'])->first();
        //check password
        if(!$user || !Hash::check($request['password'], $user->password)){
            return response([
                'message' => 'Can not Log in'
            ] , 401);
        }
        //if good then log in, create token from user
        $token = $user->createToken('myapptoken')->plainTextToken;

        //create response from user and token
        $response = [
            'user' => $user ,
            'token' => $token
        ];
        return response()->json($response , 201);
    }

    //create log out function
    public function logout(Request $request)
    {
        //delete token from user
        $request->user()->currentAccessToken()->delete();
        //create response from message
        return response([
            'message' => 'Logged out'
        ] , 200);
    }

}
