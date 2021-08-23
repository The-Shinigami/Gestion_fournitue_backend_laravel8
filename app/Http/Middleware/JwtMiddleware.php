<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use \Tymon\JWTAuth\Exceptions\TokenInvalidException;
use \Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( Request $request, Closure $next, ...$roles)
    {
        try {
            $user_ = JWTAuth::parseToken()->authenticate();
            $user = Auth::guard('utilisateur-api')->user();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException){
                return response()->json(['status' => 'Token is Invalid']);
            }else if ($e instanceof TokenExpiredException){
                return response()->json(['status' => 'Token is Expired']);
            }else{
                return response()->json(['status' => 'Authorization Token not found']);
            }
        }
        if ($user && in_array($user->role, $roles)) {
            return $next($request);
        } else {
            return response()->json(['status' => 'You are not allowed to access this route']);
        }
    }
}