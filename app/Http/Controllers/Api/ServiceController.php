<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceBooking;

class ServiceController extends Controller
{
    public function getServices()
    {
        $services = Service::with('type')
            ->where('status', 1)
            ->get();
    
        return response()->json([
            'success' => true,
            'data' => $services
        ], 200);
    }

    public function serviceBookingStore(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'additional_address_id' => 'required|exists:additional_addresses,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);
    
        $booking = ServiceBooking::create([
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'additional_address_id' => $request->additional_address_id,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
        ]);
    
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $storagePath = public_path('images/service');
                $image->move($storagePath, $filename);
    
                $booking->images()->create([
                    'image' => $filename
                ]);
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking->load('images')
        ]);
    }

    public function serviceBookingIndex()
    {
        $bookings = ServiceBooking::with(['service.type', 'images'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ], 200);
    }

    public function serviceBookingUpdate(Request $request, $id)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'additional_address_id' => 'required|exists:additional_addresses,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $booking = ServiceBooking::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $booking->update([
            'service_id' => $request->service_id,
            'additional_address_id' => $request->additional_address_id,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
        ]);

        if ($request->hasFile('images')) {
            foreach ($booking->images as $img) {
                $imagePath = public_path('images/service/' . $img->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $img->delete();
            }

            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $storagePath = public_path('images/service');
                $image->move($storagePath, $filename);

                $booking->images()->create([
                    'image' => $filename
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking->load('images')
        ]);
    }

    public function serviceBookingDelete($id)
    {
        $booking = ServiceBooking::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        foreach ($booking->images as $img) {
            $imagePath = public_path('images/service/' . $img->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $img->delete();
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking and images deleted successfully'
        ]);
    }

}
