<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller (Forgot Password)
    |--------------------------------------------------------------------------
    |
    | Ito ay para lang sa pag-handle ng pag-send ng password reset emails.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * I-override ang default view para gamitin ang ginawa nating design.
     * Hahanapin nito ang 'resources/views/auth/forgot-password.blade.php'
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }
}