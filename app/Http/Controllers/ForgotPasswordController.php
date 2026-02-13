<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /* Show the form where user enters their email. */
    
    public function showLinkRequestForm()
    {
        return view('forgot-password');
    }

    /* 2. Handle the form submission (Send OTP). */
    public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $otp = rand(100000, 999999);

    Cache::put('otp_' . $request->email, $otp, 600);

    try {
        // Change Mail::raw to Mail::send to use a styled template
        Mail::send('emails.otp_mail', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('SubdiRent - Your Verification Code');
        });
    } catch (\Exception $e) {
        return back()->withErrors(['email' => 'Failed to send email.']);
    }

    return redirect()->route('password.otp.verify.form')
                     ->with('otp_email', $request->email);
}

    /**
     * 3. Show the form where the user enters the OTP code.
     */
    public function showOtpVerifyForm()
    {

        $email = session('otp_email');


        if (!$email) {
            return redirect()->route('password.request')
                             ->with('error', 'Session expired. Please try again.');
        }

        return view('verify-otp', compact('email'));
    }
    
    /**
     * 4. Verify the OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric'
        ]);
    
        $cachedOtp = Cache::get('otp_' . $request->email);
    
        if ($cachedOtp && $cachedOtp == $request->otp) {
            
            Cache::forget('otp_' . $request->email);

            return redirect()->route('password.reset.form')
                             ->with('verified_email', $request->email);
        }
    

        return back()->with('error', 'Invalid or expired OTP code.')
                     ->with('otp_email', $request->email);
    }
    
    /**
     * 5. Show the Reset Password Form
     */
    public function showResetPasswordForm()
    {

        $email = session('verified_email');
        

        if (!$email) {
            return redirect()->route('login')->with('error', 'Session expired or invalid access.');
        }
        
        return view('reset-password', compact('email'));
    }
    
    /**
     * 6. Update the Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $request->email)->first();
        
        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        Cache::forget('otp_' . $request->email);


        return redirect()->route('home')
    ->with('status', 'Password changed successfully! Please log in.')
    ->with('open_login', true);
    }
}