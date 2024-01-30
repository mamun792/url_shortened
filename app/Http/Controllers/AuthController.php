<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;





class AuthController extends Controller
{
    public function login(LoginRequest $request)
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
                'Response' => Response::HTTP_OK,
            'message' => 'Successfully logged in'
         ]);
    } 
    
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'status' => 'success',
            'Response' => Response::HTTP_OK,
            'message' => 'Successfully logged out'
        ]);
    }

    public function refresh()
    {
       if(!auth::check()){
           return response()->json([
                'status' => 'error',
                'Response' => Response::HTTP_UNAUTHORIZED,
                'message' => 'User not found'
           ]);
       }
        $newToken=auth()->refresh();
       try{
        return response()->json([
            'token' => $newToken,
            'user' => Auth::user(),
            'status' => 'success',
            'Response' => Response::HTTP_OK,
            'message' => 'Successfully refreshed token'
        ]);
       }catch(JWTException $e){
        return response()->json([
            'status' => 'error',
            'Response' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'error' => 'could_not_refresh_token']
        );
       }catch(TokenInvalidException $e){
        return response()->json([
            'status' => 'error',
            'Response' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'error' => 'token_invalid']
        );
       }catch(TokenExpiredException $e){
        return response()->json([
            'status' => 'error',
            'Response' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'error' => 'token_expired']
        );
         }catch(TokenBlacklistedException $e){
        return response()->json([
            'status' => 'error',
            'Response' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'error' => 'token_blacklisted']);
         }
}

    public function profile()
    {
        
        try {
          
           if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'Response' => Response::HTTP_UNAUTHORIZED,
                'error' => 'Unauthorized']);
        }
    
     
        $user = Auth::user();
    
       
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'Response' => Response::HTTP_NOT_FOUND,
                'error' => 'User not found']);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'User profile',
            'user' => $user,
            'data' => $user
        ]);
        }catch(TokenInvalidException $e){
            return response()->json([
                'status' => 'error',
                'Response' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'token_invalid'
            ]);
           }catch(TokenExpiredException $e){
            return response()->json([
                'status' => 'error',
                'Response' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'token_expired']
            );
             }
            
    }
}
