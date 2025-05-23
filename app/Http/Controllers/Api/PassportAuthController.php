<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\MailContentType;


class PassportAuthController extends Controller
{

    public function requestRegistrationToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existingUser  = User::where('email', $request->email)->first();
        if ($existingUser ) {
            return response()->json(['message' => 'This email is already registered.'], 409);
        }

        $otp = rand(100000, 999999);
        $cachedOtp = Cache::put('registration_otp_' . $request->email, $otp, now()->addMinutes(10));

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

        return response()->json(['message' => 'OTP sent to your email. Please check your inbox. OTP will expire in 10 minutes.']);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cachedOtp = Cache::get('registration_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        Cache::forget('registration_otp_' . $request->email);

        $token = $user->createToken('AppName')->accessToken;

        return response()->json(['message' => 'Registration successful.', 'token' => $token, 'userId' => $user->id], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('AppName')->accessToken;
            $userId = Auth::user()->id;
            return response()->json(['message' => 'Login successful.', 'token' => $token, 'userId' => $userId], 200);
        }

        return response()->json(['message' => 'Invalid credentials.', 'error' => 'Unauthenticated'], 401);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } 
        else {
            return response()->json(['message' => 'Not authenticated'], 401);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'new_password_confirmation' => 'required|string|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully.'], 200);
    }

    public function checkUser($id)
    {
        $chkuser = User::where('id', $id)->first();
        
        if ($chkuser) {
            return response()->json([
                'status' => 200,
                'message' => 'User Available',
                'userdetails' => $chkuser,
            ], 200);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Inactive user or User name not found.',
            ], 200);
        }
    }

    public function requestPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        $otp = rand(100000, 999999);
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(10));
    
        $mailContentType = MailContentType::where('name', 'OTP')->first();
    
        if ($mailContentType && $mailContentType->mailContent) {
            $mailContent = $mailContentType->mailContent;
            $subject = $mailContent->subject ?? 'Password Reset OTP';
            $body = str_replace('{otp}', $otp, $mailContent->content);
            Mail::html($body, function ($msg) use ($request, $subject) {
                $msg->to($request->email)->subject($subject);
            });
        } else {
            Mail::raw("Your OTP for password reset is: $otp", function ($msg) use ($request) {
                $msg->to($request->email)->subject('Password Reset OTP');
            });
        }
    
        return response()->json(['message' => 'OTP sent to your email. Please check your inbox. OTP will expire in 10 minutes.']);
    }    

    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        Cache::put('otp_verified_' . $request->email, true, now()->addMinutes(10));

        return response()->json(['message' => 'OTP verified. You can now reset your password.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Cache::get('otp_verified_' . $request->email)) {
            return response()->json(['message' => 'OTP not verified.'], 401);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'User not found.'], 404);

        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget('otp_' . $request->email);
        Cache::forget('otp_verified_' . $request->email);

        return response()->json(['message' => 'Password reset successful.']);
    }

}
