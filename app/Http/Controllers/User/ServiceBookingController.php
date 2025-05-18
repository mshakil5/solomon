<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceBooking;
use App\Models\AdditionalAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\CompanyDetails;
use App\Models\ServiceImage;
use App\Models\Invoice;

class ServiceBookingController extends Controller
{

    public function userBookings()
    {
        $userId = auth()->id();
        $bookings = ServiceBooking::with(['service', 'invoices'])
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('user.service_bookings.index', compact('bookings'));
    }

    public function showBookingDetails($id)
    {
        $booking = ServiceBooking::with([
                'service', 
                'billingAddress', 
                'shippingAddress',
                'files',
                'invoices.transaction'
            ])->findOrFail($id);
            
        if ($booking->user_id != auth()->id()) {
            abort(403);
        }
        
        return view('user.show_booking.details', compact('booking'));
    }

    public function showInvoice(ServiceBooking $serviceBooking)
    {
        $invoices = $serviceBooking->invoices;
        return view('user.invoice.details', compact('invoices'));
    }

    public function editBooking($id)
    {
        $booking = ServiceBooking::with(['service', 'files'])
            ->where('user_id', auth()->id())
            ->where('status', 1)
            ->findOrFail($id);

        $shippingAddresses = AdditionalAddress::where('user_id', auth()->id())
            ->where('type', 1)
            ->latest()
            ->get();

        $billingAddresses = AdditionalAddress::where('user_id', auth()->id())
            ->where('type', 2)
            ->latest()
            ->get();

        return view('frontend.service_booking_edit', compact('booking', 'shippingAddresses', 'billingAddresses'));
    }

    public function updateBooking(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'billing_address_id' => 'required|exists:additional_addresses,id',
            'shipping_address_id' => 'required|exists:additional_addresses,id',
            'files.*' => 'nullable|file|max:10240',
            'remove_files' => 'nullable|array',
            'remove_files.*' => 'exists:service_images,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $booking = ServiceBooking::where('user_id', auth()->id())
            ->where('status', 1)
            ->findOrFail($id);

        $now = now();
        $serviceDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);
        $diffInMinutes = $now->diffInMinutes($serviceDateTime, false);
        $hour = $serviceDateTime->format('H');
        $dayOfWeek = $serviceDateTime->dayOfWeek;

        $company = CompanyDetails::select('opening_time', 'closing_time')->first();
        $openingHour = $company?->opening_time ?? '10:00';
        $closingHour = $company?->closing_time ?? '18:00';

        $opening = Carbon::createFromFormat('H:i', $openingHour)->format('H');
        $closing = Carbon::createFromFormat('H:i', $closingHour)->format('H');

        if ($serviceDateTime->isToday() && $diffInMinutes >= 0 && $diffInMinutes <= 120) {
            $type = 1; $fee = 400.00;
        } elseif ($serviceDateTime->isToday() && $diffInMinutes > 120) {
            $type = 2; $fee = 250.00;
        } elseif ($dayOfWeek === 0 || $hour < $opening || $hour >= $closing) {
            $type = 3; $fee = 300.00;
        } else {
            $type = 4; $fee = 0.00;
        }

        $totalFee = $booking->service_fee + $fee;

        $booking->update([
            'billing_address_id' => $request->billing_address_id,
            'shipping_address_id' => $request->shipping_address_id,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'additional_fee' => $fee,
            'total_fee' => $totalFee,
            'type' => $type
        ]);

        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $fileId) {
                $file = ServiceImage::find($fileId);
                if ($file && $file->service_booking_id == $booking->id) {
                    $filePath = public_path('images/service/' . $file->file);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $file->delete();
                }
            }
        }

        if ($request->hasFile('files')) {
            $files = is_array($request->file('files')) ? $request->file('files') : [$request->file('files')];
        
            foreach ($files as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $storagePath = public_path('images/service');
                $file->move($storagePath, $filename);
        
                $booking->files()->create([
                    'file' => $filename
                ]);
            }
        }

        return redirect()->route('user.service.bookings')->with('success', 'Booking updated successfully.');
    }
}
