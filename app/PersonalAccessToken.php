<?php

namespace App;

use Laravel\Sanctum\PersonalAccessToken as Model;
 
class PersonalAccessToken extends Model
{
    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'token',
        'access_ip',
        'abilities',
        'expired_at'
    ];

    protected function isValidAccessToken($accessToken): bool
    {
        if (! $accessToken) {
            return false;
        }   

        $isValid =
            (! $this->expiration || $accessToken->created_at->gt(now()->subMinutes($this->expiration)))
            && $this->hasValidProvider($accessToken->tokenable);

        if (is_callable(Sanctum::$accessTokenAuthenticationCallback)) {
            $isValid = (bool) (Sanctum::$accessTokenAuthenticationCallback)($accessToken, $isValid);
        }

        return $isValid;
    }

    // public function changeDateExpired(){
    //     // $request->user()->currentAccessToken()->expired_at = now()->addDays(10);
    //     // $request->user()->currentAccessToken()->save();
    //     $this->expired_at = now()->addHour(1);
    //     $this->save();
    // }
}
