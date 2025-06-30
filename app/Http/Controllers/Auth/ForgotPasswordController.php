<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'email.required' => 'Email-ul este obligatoriu.',
            'email.email' => 'Email-ul trebuie să fie valid.',
            'email.exists' => 'Acest email nu este înregistrat.',
        ] : [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be valid.',
            'email.exists' => 'This email is not registered.',
        ];

        $request->validate(['email' => 'required|email|exists:users,email'], $messages);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            $statusMsg = $lang == 'ro'
                ? 'Link-ul de resetare a parolei a fost trimis.'
                : trans($response);
            return back()->with('status', $statusMsg);
        } else {
            $errorMsg = $lang == 'ro'
                ? 'Eroare la trimiterea link-ului de resetare.'
                : trans($response);
            return back()->withErrors(['email' => $errorMsg]);
        }
    }
}
