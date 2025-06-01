<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\ServiceBookingReview;
use App\Models\Type;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\CompanyDetails;
use App\Models\Invoice;

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
    
        $services = $services->map(function ($item) {
            $item->des_english = strip_tags($item->des_english);
            $item->des_romanian = strip_tags($item->des_romanian);
            $item->information = strip_tags($item->information);
    
            if ($item->type) {
                $item->type->des_english = strip_tags($item->type->des_english);
                $item->type->des_romanian = strip_tags($item->type->des_romanian);
                $item->type->information = strip_tags($item->type->information);
            }
    
            return $item;
        });
    
        return response()->json([
            'success' => true,
            'data' => $services
        ], 200);
    }

    public function getTypes()
    {
        $types = Type::with('services')
            ->where('status', 1)
            ->get();
    
        if ($types->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No types found.'
            ], 404);
        }
    
        $types = $types->map(function ($type) {
            $type->des_english = strip_tags($type->des_english);
            $type->des_romanian = strip_tags($type->des_romanian);
            $type->information = strip_tags($type->information ?? '');
    
            $type->services = $type->services->map(function ($service) {
                $service->des_english = strip_tags($service->des_english);
                $service->des_romanian = strip_tags($service->des_romanian);
                $service->information = strip_tags($service->information);
                return $service;
            });
    
            return $type;
        });
    
        return response()->json([
            'success' => true,
            'data' => $types
        ], 200);
    }

    public function serviceBookingStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'billing_address_id' => 'required|exists:additional_addresses,id',
            'shipping_address_id' => 'required|exists:additional_addresses,id',
            'files.*' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

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

        $service = Service::findOrFail($request->service_id);
        $serviceFee = $service->price;
        $totalFee = $serviceFee + $fee;

        $booking = ServiceBooking::create([
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'billing_address_id' => $request->billing_address_id,
            'shipping_address_id' => $request->shipping_address_id,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'service_fee' => $serviceFee,
            'additional_fee' => $fee,
            'total_fee' => $totalFee,
            'type' => $type
        ]);
    
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
    
        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking->load('files')
        ]);
    }

    public function serviceBookingIndex()
    {
      $bookings = ServiceBooking::with(['service.type', 'files', 'serviceReview','shippingAddress', 'billingAddress'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        
        $bookings = $bookings->map(function ($booking) {
            if ($booking->service) {
                $booking->service->des_english = strip_tags($booking->service->des_english);
                $booking->service->des_romanian = strip_tags($booking->service->des_romanian);
                $booking->service->information = strip_tags($booking->service->information);
        
                if ($booking->service->type) {
                    $booking->service->type->des_english = strip_tags($booking->service->type->des_english);
                    $booking->service->type->des_romanian = strip_tags($booking->service->type->des_romanian);
                    $booking->service->type->information = strip_tags($booking->service->type->information ?? '');
                }
            }
            return $booking;
        });
  

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
        $booking = ServiceBooking::with(['service.type', 'files', 'serviceReview', 'user.additionalAddresses', 'address'])
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
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'billing_address_id' => 'required|exists:additional_addresses,id',
            'shipping_address_id' => 'required|exists:additional_addresses,id',
            'files.*' => 'nullable|file|max:10240',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

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
  
        $booking = ServiceBooking::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized access.',
            ], 404);
        }

        $service = Service::findOrFail($request->service_id);
        $serviceFee = $service->price;
        $totalFee = $serviceFee + $fee;

        $booking->update([
            'service_id' => $request->service_id,
            'billing_address_id' => $request->billing_address_id,
            'shipping_address_id' => $request->shipping_address_id,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'additional_fee' => $fee,
            'type' => $type
        ]);

        if ($request->hasFile('files')) {
            foreach ($booking->files as $file) {
                $filePath = public_path('images/service/' . $file->file);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $file->delete();
            }
        
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

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking->load('files')
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

        foreach ($booking->files as $file) {
            $filePath = public_path('images/service/' . $file->file);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $file->delete();
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking and images deleted successfully'
        ]);
    }

    public function reviewStore(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'review_star' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
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

    public function getInvoices($id)
    {
        $booking = ServiceBooking::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$booking) {
            return response()->json(['error' => 'Unauthorized or booking not found.'], 403);
        }

        $invoices = Invoice::where('service_booking_id', $id)
        ->select('id', 'service_booking_id', 'invoiceid', 'amount', 'date', 'img')
        ->get();    

        return response()->json($invoices);
    }

    public function cancelBooking($id)
    {
        $booking = ServiceBooking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        if ($booking->status != 1) {
            return response()->json(['message' => 'Only placed bookings can be cancelled'], 403);
        }

        $booking->status = 4;
        $booking->save();

        return response()->json(
          ['message' => 'Booking cancelled successfully'
          , 'data' => $booking
          ,'status' => 200]);
    }


}
