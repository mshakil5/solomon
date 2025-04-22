<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceBooking;

class ServiceBookingController extends Controller
{
    public function newServiceBooking()
    {
        $bookings = ServiceBooking::with(['service.type', 'files', 'user.additionalAddresses', 'address'])->where('status', 1)->latest()->get();
        return view('admin.service-booking.all-service-booking', compact('bookings'));
    }

    public function processingServiceBooking()
    {
        $bookings = ServiceBooking::with(['service.type', 'files', 'user.additionalAddresses', 'address'])->where('status', 2)->latest()->get();
        return view('admin.service-booking.all-service-booking', compact('bookings'));
    }

    public function completedServiceBooking()
    {
        $bookings = ServiceBooking::with(['service.type', 'files', 'user.additionalAddresses', 'address'])->where('status', 3)->latest()->get();
        return view('admin.service-booking.all-service-booking', compact('bookings'));
    }

    public function cancelledServiceBooking()
    {
        $bookings = ServiceBooking::with(['service.type', 'files', 'user.additionalAddresses', 'address'])->where('status', 4)->latest()->get();
        return view('admin.service-booking.all-service-booking', compact('bookings'));
    }

    public function allServiceBooking()
    {
        $bookings = ServiceBooking::with(['service.type', 'files', 'user.additionalAddresses', 'address'])->latest()->get();
        return view('admin.service-booking.all-service-booking', compact('bookings'));
    }

    public function changeBookingStatus(Request $request)
    {
        $booking = ServiceBooking::find($request->id);
        $booking->status = $request->status;

        if ($booking->save()) {

            $message = "Status Changed Successfully.";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "There was an error to change status!!.";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    }

}
