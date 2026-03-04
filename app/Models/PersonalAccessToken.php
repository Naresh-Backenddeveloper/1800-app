<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Find the token instance matching the given token.
     *
     * @param  string  $token
     * @return static|null
     */
    public static function findToken($token)
    {
        return static::where('token', hash('sha256', $token))->first();
    }

    /**
     * Find the token instance matching the given token.
     *
     * @param  string  $token
     * @return User|null
     */
    public static function findUser($token)
    {
        $token = static::findToken($token);
        if ($token != null) {
            return User::find($token->user_id);
        }
        return null;
    }
}
