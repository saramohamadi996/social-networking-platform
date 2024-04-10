<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class UserLoginService
{
    /**
     * Authenticate user using credentials.
     * @param array $credentials
     * @return Authenticatable|null
     */
    public function authenticate(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            return Auth::user();
        }
        return null;
    }
}
