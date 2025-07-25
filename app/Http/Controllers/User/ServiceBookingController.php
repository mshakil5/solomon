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
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceBookingReview;

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
                'invoices.transaction',
                'transactions',
                'serviceReview'
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
            // 'date' => 'required|date',
            // 'time' => 'required',
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

        $booking->update([
            'billing_address_id' => $request->billing_address_id,
            'shipping_address_id' => $request->shipping_address_id,
            'description' => $request->description,
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

    public function cancelBooking(Request $request, $id)
    {
        $lang = session('app_locale', 'ro');

        $booking = ServiceBooking::findOrFail($id);

        if ($booking->user_id != Auth::id()) {
            return redirect()->back()->with('error', $lang
                ? 'Rezervarea nu a fost găsită'
                : 'Booking not found');
        }

        if ($booking->status != 1) {
            return redirect()->back()->with('error', $lang
                ? 'Rezervarea nu poate fi anulată în stadiul actual'
                : 'Booking cannot be cancelled in its current status');
        }

        $booking->update(['status' => 4]);

        return redirect()->route('user.service.bookings')->with('success', $lang
            ? 'Rezervarea a fost anulată cu succes'
            : 'Booking cancelled successfully');
    }

    public function reviewStore(Request $request, $id)
    {
        $request->validate([
            'review_star' => 'required|integer|min:1|max:5',
        ]);

        $booking = ServiceBooking::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$booking) {
            return back()->with('error', 'Booking not found.');
        }

        if ($booking->status != 3) {
            return back()->with('error', 'You can only review completed bookings.');
        }

        if ($booking->serviceReview) {
            return back()->with('error', 'You have already reviewed this booking.');
        }

        ServiceBookingReview::create([
            'service_booking_id' => $id,
            'review_star' => $request->review_star,
        ]);

        return back()->with('success', 'Review submitted successfully.');
    }

}
