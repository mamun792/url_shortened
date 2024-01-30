<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Response;


class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
      
        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(
            [
            'message' => 'User created successfully',
            'token' => $token, 
            'status' => 'success',
            'Response' => Response::HTTP_OK,
            ]
        );
        
    }
}
