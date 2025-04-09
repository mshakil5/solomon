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


class PassportAuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|regex:/^\d{10}$/',
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
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
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
        ]);

        $token = $user->createToken('AppName')->accessToken;
        $userId = $user;

        return response()->json(['message' => 'Registration successful.', 'token' => $token, 'userId' => $userId], 200);
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
        if (!$user) return response()->json(['message' => 'User not found.'], 404);

        $otp = rand(100000, 999999);
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(10));

        Mail::raw("Your OTP is: $otp", function ($msg) use ($request) {
            $msg->to($request->email)->subject('Password Reset OTP');
        });

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
