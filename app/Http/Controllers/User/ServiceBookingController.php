<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceBooking;

class ServiceBookingController extends Controller
{

    public function userBookings()
    {
        $userId = auth()->id();
        $bookings = ServiceBooking::with('service')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->get();
            
        return view('user.service_bookings.index', compact('bookings'));
    }

    public function showBookingDetails($id)
    {
        $booking = ServiceBooking::with([
                'service', 
                'billingAddress', 
                'shippingAddress',
                'files',
                'invoices'
            ])->findOrFail($id);
            
        if ($booking->user_id != auth()->id()) {
            abort(403);
        }
        
        return view('user.show_booking.details', compact('booking'));
    }

    public function showInvoice($id)
    {
        $invoice = Invoice::where('service_booking_id', $id)
            ->firstOrFail();
            
        $booking = ServiceBooking::find($invoice->service_booking_id);
        if ($booking->user_id != auth()->id()) {
            abort(403);
        }
        
        return response()->file(public_path($invoice->img));
    }
}
