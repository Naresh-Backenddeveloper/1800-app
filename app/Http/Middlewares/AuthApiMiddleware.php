<?php

namespace App\Http\Middlewares;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if ($token == null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = PersonalAccessToken::findUser($token);
        if ($user == null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }    
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        return $next($request);
    }
}
