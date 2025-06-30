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
        $lang = session('app_locale', 'ro');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ], $lang == 'ro' ? [
            'email.required' => 'Email este obligatoriu.',
            'email.email' => 'Email-ul trebuie să fie valid.',
        ] : [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            $errorMsg = $lang == 'ro' 
                ? 'Acest email este deja înregistrat.' 
                : 'This email is already registered.';
            return redirect()->back()
                ->with('error', $errorMsg)
                ->withInput();
        }

        $otp = rand(100000, 999999);
        Cache::put('registration_otp_' . $request->email, $otp, now()->addMinutes(10));

        $mailContentType = MailContentType::where('name', 'OTP')->first();

        if ($mailContentType && $mailContentType->mailContent) {
            $mailContent = $mailContentType->mailContent;
            $subject = $mailContent->subject ?? ($lang == 'ro' ? 'Cod OTP pentru înregistrare' : 'Registration OTP');
            $body = str_replace('{otp}', $otp, $mailContent->content);

            Mail::html($body, function ($msg) use ($request, $subject) {
                $msg->to($request->email)->subject($subject);
            });
        } else {
            $defaultSubject = $lang == 'ro' ? 'Cod OTP pentru înregistrare' : 'Registration OTP';
            $defaultBody = $lang == 'ro' 
                ? "Codul tău OTP pentru înregistrare este: $otp" 
                : "Your OTP for registration is: $otp";

            Mail::raw($defaultBody, function ($msg) use ($request, $defaultSubject) {
                $msg->to($request->email)->subject($defaultSubject);
            });
        }

        $successMsg = $lang == 'ro' 
            ? 'OTP a fost trimis pe emailul tău. Verifică inbox-ul.' 
            : 'OTP sent to your email. Please check your inbox.';

        return redirect()->back()
            ->with('success', $successMsg)
            ->with('email', $request->email)
            ->with('otp_sent', true);
    }

    public function verifyOtp(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'email.required' => 'Email este obligatoriu.',
            'email.email' => 'Email-ul trebuie să fie valid.',
            'otp.required' => 'Codul OTP este obligatoriu.',
            'otp.digits' => 'Codul OTP trebuie să aibă 6 cifre.',
        ] : [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be valid.',
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be 6 digits.',
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cachedOtp = Cache::get('registration_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            $errorMsg = $lang == 'ro' 
                ? 'Codul OTP este invalid sau a expirat.' 
                : 'Invalid or expired OTP.';
            return redirect()->back()
                ->with('error', $errorMsg)
                ->withInput();
        }

        $successMsg = $lang == 'ro' 
            ? 'OTP a fost verificat cu succes. Completează înregistrarea.' 
            : 'OTP verified successfully. Please complete your registration.';

        return redirect()->back()
            ->with('success', $successMsg)
            ->with('email', $request->email)
            ->with('otp_verified', true);
    }

    public function register(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'name.required' => 'Numele este obligatoriu.',
            'name.string' => 'Numele trebuie să fie text.',
            'name.max' => 'Numele nu poate depăși 255 caractere.',
            'surname.string' => 'Prenumele trebuie să fie text.',
            'email.required' => 'Email-ul este obligatoriu.',
            'email.email' => 'Email-ul trebuie să fie valid.',
            'email.unique' => 'Email-ul este deja folosit.',
            'phone.required' => 'Numărul de telefon este obligatoriu.',
            'phone.regex' => 'Numărul de telefon trebuie să conțină exact 10 cifre.',
            'password.required' => 'Parola este obligatorie.',
            'password.min' => 'Parola trebuie să aibă cel puțin 6 caractere.',
            'password.confirmed' => 'Confirmarea parolei nu corespunde.',
            // add others as needed
        ] : [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'surname.string' => 'Surname must be a string.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be valid.',
            'email.unique' => 'Email has already been taken.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'The phone number must be exactly 10 digits.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            // add others as needed
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => ['required', 'regex:/^\d{10}$/'],
            'address_first_line' => 'nullable|string|max:255',
            'address_second_line' => 'nullable|string|max:255',
            'address_third_line' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ], $messages);

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