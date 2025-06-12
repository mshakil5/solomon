<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Models\MailContentType;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return redirect()->back()
                ->with('error', 'This email is already registered.')
                ->withInput();
        }

        $otp = rand(100000, 999999);
        Cache::put('registration_otp_' . $request->email, $otp, now()->addMinutes(10));

        $mailContentType = MailContentType::where('name', 'OTP')->first();

        if ($mailContentType && $mailContentType->mailContent) {
            $mailContent = $mailContentType->mailContent;
            $subject = $mailContent->subject ?? 'Registration OTP';
            $body = str_replace('{otp}', $otp, $mailContent->content);

            Mail::html($body, function ($msg) use ($request, $subject) {
                $msg->to($request->email)->subject($subject);
            });
        } else {
            Mail::raw("Your OTP for registration is: $otp", function ($msg) use ($request) {
                $msg->to($request->email)->subject('Registration OTP');
            });
        }

        return redirect()->back()
            ->with('success', 'OTP sent to your email. Please check your inbox.')
            ->with('email', $request->email)
            ->with('otp_sent', true);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Invalid OTP format')
                ->withInput();
        }

        $cachedOtp = Cache::get('registration_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return redirect()->back()
                ->with('error', 'Invalid or expired OTP.')
                ->withInput();
        }

        return redirect()->back()
            ->with('success', 'OTP verified successfully. Please complete your registration.')
            ->with('email', $request->email)
            ->with('otp_verified', true);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|regex:/^\d{10}$/',
            'address_first_line' => 'nullable|string|max:255',
            'address_second_line' => 'nullable|string|max:255',
            'address_third_line' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'phone.regex' => 'The phone number must be exactly 10 digits.',
            'email.unique' => 'The email has already been taken.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
              ->withErrors($validator)
              ->withInput()
              ->with('otp_verified', true);
        }

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address_first_line' => $request->address_first_line,
            'address_second_line' => $request->address_second_line,
            'address_third_line' => $request->address_third_line,
            'town' => $request->town,
            'postcode' => $request->postcode,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect($this->redirectPath());
    }

}