<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\ServiceBookingReview;

class ServiceController extends Controller
{
    public function getServices()
    {
        $services = Service::with('type')
            ->where('status', 1)
            ->get();
    
        if ($services->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No services found.'
            ], 404);
        }
    
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
            'images.*' => 'nullable|file|max:10240',
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

        if (!$bookings) {
            return response()->json([
                'success' => false,
                'message' => 'No bookings found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bookings
        ], 200);
    }

    public function serviceBookingDetails($id)
    {
        $booking = ServiceBooking::with(['service.type', 'images', 'serviceReview'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

          if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized access.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
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
            'images.*' => 'nullable|file|max:10240',
        ]);

        $booking = ServiceBooking::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized access.',
            ], 404);
        }

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
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized access.',
            ], 404);
        }

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

    public function reviewStore(Request $request, $id)
    {
        $request->validate([
            'review_star' => 'required|integer|min:1|max:5',
        ]);
        
        $serviceBooking = ServiceBooking::find($id);
        
        if (!$serviceBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Service booking not found.',
            ], 404);
        }
    
        $existingReview = ServiceBookingReview::where('service_booking_id', $id)
                                              ->first();
    
        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this service booking.',
            ], 400);
        }
    
        $review = ServiceBookingReview::create([
            'service_booking_id' => $id,
            'review_star' => $request->review_star,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully.',
            'data' => $review
        ], 201);
    }  

}
