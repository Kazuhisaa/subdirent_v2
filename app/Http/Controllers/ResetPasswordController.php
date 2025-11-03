<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller (Reset Password)
    |--------------------------------------------------------------------------
    |
    | Ito ay para lang sa pag-handle ng mismong pag-reset ng password.
    |
    */

    use ResetsPasswords;

    /**
     * Saan pupunta ang user pagkatapos niyang mag-reset ng password.
     * Palitan mo ito kung saan mo sila gustong i-redirect.
     *
     * @var string
     */
    protected $redirectTo = '/'; // Redirect sa homepage

    /**
     * I-override ang default view para gamitin ang ginawa nating design.
     * Hahanapin nito ang 'resources/views/auth/reset-password.blade.php'
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}