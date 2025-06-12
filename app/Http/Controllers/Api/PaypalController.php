<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use App\Mail\PaymentSuccessUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\ServiceBooking;

class PaypalController extends Controller
{
    public function payment(Request $request)
    {
        $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
        ], [
            'invoice_id.required' => 'Invoice ID is required.',
            'invoice_id.exists' => 'The specified invoice does not exist.',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        
        $booking = ServiceBooking::where('id', $invoice->service_booking_id)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

        $user = Auth::user();
        $name = $user->name;
        $phone = $user->phone;
        $email = $user->email;
        $amount = $invoice->amount;

        $paypalcommission = $amount * 2.9/100;
        $fixedFee = .30;
        $amt = $amount - $paypalcommission - $fixedFee;

        $payment = new Payment();
        $payment->user_id = $user->id;
        $payment->payment_id = $request->payment_id;
        $payment->payer_id = $request->payer_id;
        $payment->payer_email = $request->payer_email;
        $payment->amount = $amount;
        $payment->currency = env('PAYPAL_CURRENCY');
        $payment->payment_status = $request->state;
        $payment->save();

        $transaction = new Transaction();
        $transaction->date = now()->format('Y-m-d');
        $transaction->user_id = $user->id;
        $transaction->invoice_id = $invoice->id;
        $transaction->amount = $amount;
        $transaction->net_amount = $amt;
        $transaction->tranid = now()->timestamp . $user->id;
        $transaction->save();

        $invoice->status = 0; // 0 means paid
        $invoice->save();

        $adminmail = Contact::where('id', 1)->first()->email;

        // Mail::to($adminmail)->send(new PaymentSuccessUser($user, $payment));

        // Mail::to($email)->send(new PaymentSuccessUser($user, $payment));

        return response()->json([
            'success' => true,
            'message' => 'Payment successful.',
            'data' => [
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'status' => 'paid'
            ]
        ], 200);
    }
}
