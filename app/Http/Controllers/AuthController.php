<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;



class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
       try {
       if(! $token = JWTAuth::attempt($credentials)){
           return response()->json(['error' => 'invalid_credentials'], 400);
       }
       } catch (JWTException $e) {
           return response()->json(['error' => 'could_not_create_token'], 500);
       } 
         return response()->json([
                'token' => $token,
                'user' => Auth::user(),
                'status' => 'success',
            'message' => 'Successfully logged in'
         ]);
    } 
    
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
       if(!auth::check()){
           return response()->json(['error' => 'user_not_found'], 404);
       }
        $newToken=auth()->refresh();
       try{
        return response()->json([
            'token' => $newToken,
            'user' => Auth::user(),
            'status' => 'success',
            'message' => 'Successfully refreshed token'
        ]);
       }catch(JWTException $e){
        return response()->json(['error' => 'could_not_refresh_token'], 500);
       }catch(TokenInvalidException $e){
        return response()->json(['error' => 'token_invalid'], 500);
       }catch(TokenExpiredException $e){
        return response()->json(['error' => 'token_expired'], 500);
         }catch(TokenBlacklistedException $e){
        return response()->json(['error' => 'token_blacklisted'], 500);
         }
}

    public function profile()
    {
        
        try {
          
           if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
     
        $user = Auth::user();
    
       
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'User profile',
            'user' => $user,
            'data' => $user
        ]);
        }catch(TokenInvalidException $e){
            return response()->json(['error' => 'token_invalid'], 500);
           }catch(TokenExpiredException $e){
            return response()->json(['error' => 'token_expired'], 500);
             }
            
    }
}
