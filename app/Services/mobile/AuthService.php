<?php
namespace App\Services\mobile;

use App\Exceptions\Auth\InvalidCredentialsException;
use Illuminate\Support\Facades\Auth; // Use Laravel's Auth facade

class AuthService
{
    /**
     * Attempt to log in the user with the given credentials.
     *
     * @param array $credentials
     * @return \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable
     * @throws InvalidCredentialsException
     */
    public function attemptLogin(array $credentials)
    {
        if (! Auth::attempt($credentials)) {
            throw new InvalidCredentialsException('Invalid login credentials', 401);
        }

        return Auth::user();
    }
}
