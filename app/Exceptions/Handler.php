<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            return response()->json(['status' => 'Token is Invalid'], 401);
        } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            return response()->json(['status' => 'Token is Expired'], 401);
        } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
            return response()->json(['status' => 'Token is not provided'], 401);
        } elseif ($e instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'Unauthenticated'], 401);
            }
        }
    
        return parent::render($request, $e);
    }
}
