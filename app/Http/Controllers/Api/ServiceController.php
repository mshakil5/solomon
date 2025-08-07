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
use App\Models\Holiday;
use App\Models\NewService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Mail\JobOrderMail;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;

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
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'billing_address_id' => 'required|exists:additional_addresses,id',
            'shipping_address_id' => 'required|exists:additional_addresses,id',
            'files.*' => 'required|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        
        $selectedDate = $request->date;
        $selectedTime = $request->time;

        $adjustedTime = Carbon::createFromFormat('H:i', $selectedTime)->addMinutes(2)->format('H:i');

        if ($selectedDate === now()->format('Y-m-d') && $adjustedTime <= now()->format('H:i')) {
            return response()->json([
                'status' => false,
                'message' => 'Ora trebuie să fie cu cel puțin 2 minute după ora curentă pentru astăzi.',
                'errors' => ['time' => ['Ora trebuie să fie după cea curentă.']]
            ], 422);
        }

        // Calculate type and additional fee
        $now = now();
        $serviceDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);
        $diffInMinutes = $now->diffInMinutes($serviceDateTime, false);
        $hour = (int) $serviceDateTime->format('H');
        $dayOfWeek = $serviceDateTime->dayOfWeek;

        $company = CompanyDetails::select('opening_time', 'closing_time', 'status')->first();

        if (!$company || $company->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Company is closed. Booking unavailable.'
            ], 403);
        }

        $openingHour = $company->opening_time ?? '09:00';
        $closingHour = $company->closing_time ?? '18:00';

        $opening = (int) Carbon::createFromFormat('H:i', $openingHour)->format('H');
        $closing = (int) Carbon::createFromFormat('H:i', $closingHour)->format('H');

        $typeFees = [1 => 400.00, 2 => 250.00, 3 => 300.00, 4 => 0.00];

        // Check holiday
        $monthName = $serviceDateTime->format('F');
        $day = $serviceDateTime->day;
        $holiday = Holiday::where('month', $monthName)->where('day', $day)->where('status', true)->first();


        if ($serviceDateTime && $serviceDateTime->isToday() && $diffInMinutes >= 0 && $diffInMinutes <= 121) {
            $type = 1; // Emergency
        } elseif ($holiday) {
            $type = 3; //Holiday always Outside Working Hours
        } elseif ($serviceDateTime && $dayOfWeek === 0) {
            $type = 3; // Sunday always Outside Working Hours
        } elseif ($serviceDateTime && $serviceDateTime->isToday() && $diffInMinutes > 120) {
            $type = 2; // Prioritized
        } elseif ($dayOfWeek === 0 || $hour < $opening || $hour >= $closing) {
            $type = 3; // Outside Working Hours
        } else {
            $type = 4; // Standard
        }




        $additionalFee = $typeFees[$type];

        // Create booking
        $booking = ServiceBooking::create([
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'billing_address_id' => $request->billing_address_id,
            'shipping_address_id' => $request->shipping_address_id,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'additional_fee' => $additionalFee,
            'type' => $type,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/service'), $filename);
                $booking->files()->create(['file' => $filename]);
            }
        }

        $emailText = "Rezervarea dvs. a fost trimisă cu succes.\n\n" .
                    "Vă mulțumim pentru încredere!";

        Mail::raw($emailText, function ($message) {
            $message->to(auth()->user()->email)
                    ->subject('Confirmare Rezervare');
        });

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking->load('files'),
        ]);
    }

    public function serviceBookingIndex()
    {
      $bookings = ServiceBooking::with(['service.type', 'files', 'serviceReview', 'user.additionalAddresses', 'address', 'invoices', 'shippingAddress', 'billingAddress'])
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
            'description' => 'nullable|string',
            'billing_address_id' => 'required|exists:additional_addresses,id',
            'shipping_address_id' => 'required|exists:additional_addresses,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'files.*' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $selectedDate = $request->date;
        $selectedTime = $request->time;

        if ($selectedDate === now()->format('Y-m-d') && $selectedTime <= now()->format('H:i')) {
            return response()->json([
                'status' => false,
                'message' => 'Ora trebuie să fie după ora curentă pentru astăzi.',
                'errors' => ['time' => ['Ora trebuie să fie după cea curentă.']]
            ], 422);
        }

        // Calculate type and additional fee
        $now = now();
        $serviceDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);
        $diffInMinutes = $now->diffInMinutes($serviceDateTime, false);
        $hour = (int) $serviceDateTime->format('H');
        $dayOfWeek = $serviceDateTime->dayOfWeek;

        $company = CompanyDetails::select('opening_time', 'closing_time', 'status')->first();

        if (!$company || $company->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Company is closed. Booking unavailable.'
            ], 403);
        }

        $openingHour = $company->opening_time ?? '09:00';
        $closingHour = $company->closing_time ?? '18:00';

        $opening = (int) Carbon::createFromFormat('H:i', $openingHour)->format('H');
        $closing = (int) Carbon::createFromFormat('H:i', $closingHour)->format('H');

        $typeFees = [1 => 400.00, 2 => 250.00, 3 => 300.00, 4 => 0.00];

        // Check holiday
        $monthName = $serviceDateTime->format('F');
        $day = $serviceDateTime->day;
        $holiday = Holiday::where('month', $monthName)->where('day', $day)->where('status', true)->first();

        if ($holiday || $dayOfWeek === 0) {
            $type = 3;
        } elseif ($serviceDateTime->isToday() && $diffInMinutes >= 0 && $diffInMinutes <= 120) {
            $type = 1;
        } elseif ($serviceDateTime->isToday() && $diffInMinutes > 120) {
            $type = 2;
        } elseif ($hour < $opening || $hour >= $closing) {
            $type = 3;
        } else {
            $type = 4;
        }

        $additionalFee = $typeFees[$type];



        $booking = ServiceBooking::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or unauthorized access.',
            ], 404);
        }

        $booking->update([
            'billing_address_id' => $request->billing_address_id,
            'shipping_address_id' => $request->shipping_address_id,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'additional_fee' => $additionalFee,
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

            foreach ($request->file('files') as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/service'), $filename);
                $booking->files()->create(['file' => $filename]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking->load('files'),
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

    public function calculateFee(Request $request)
    {
        $company = CompanyDetails::first();

        if ($company->status == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Company is currently closed. Fee calculation unavailable.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    $date = $request->input('date');
                    if (Carbon::parse($date)->isToday()) {
                        $combined = Carbon::createFromFormat('Y-m-d H:i', "$date $value");
                        if ($combined->lte(now())) {
                            $fail('Nu poți selecta o dată în trecut.');
                        }
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $date = $request->date;
        $time = $request->time ?? now()->format('H:i');
        $now = now();

        $typeFees = [
            1 => 400.00, // Emergency
            2 => 250.00, // Prioritized
            3 => 300.00, // Outside working hours
            4 => 0.00    // Standard
        ];

        $typeLabels = [
            1 => 'Emergency Service',
            2 => 'Prioritized Service',
            3 => 'Outside Working Hours',
            4 => 'Standard Service',
        ];

        $serviceDateTime = $time ? Carbon::createFromFormat('Y-m-d H:i', "$date $time") : null;

        if ($serviceDateTime) {
            $diffInMinutes = $now->diffInMinutes($serviceDateTime, false);
            $hour = $serviceDateTime->format('H');
            $dayOfWeek = $serviceDateTime->dayOfWeek;
        } else {
            $diffInMinutes = null;
            $hour = null;
            $dayOfWeek = null;
        }

        $company = CompanyDetails::select('opening_time', 'closing_time')->first();
        $openingHour = $company?->opening_time ?? '09:00';
        $closingHour = $company?->closing_time ?? '18:00';

        $opening = (int) Carbon::createFromFormat('H:i', $openingHour)->format('H');
        $closing = (int) Carbon::createFromFormat('H:i', $closingHour)->format('H');

        $monthName = $serviceDateTime->format('F');
        $day = $serviceDateTime->day;

        $holiday = Holiday::where('month', $monthName)
                  ->where('day', $day)
                  ->where('status', true)
                  ->first();

        if ($serviceDateTime && $serviceDateTime->isToday() && $diffInMinutes >= 0 && $diffInMinutes <= 121) {
            $type = 1; // Emergency
        } elseif ($holiday) {
            $type = 3; //Holiday always Outside Working Hours
        } elseif ($serviceDateTime && $dayOfWeek === 0) {
            $type = 3; // Sunday always Outside Working Hours
        } elseif ($dayOfWeek === 0 || $hour < $opening || $hour >= $closing) {
            $type = 3; // Outside Working Hours
        } elseif ($serviceDateTime && $serviceDateTime->isToday() && $diffInMinutes > 120) {
            $type = 2; // Prioritized
        } else {
            $type = 4; // Standard
        }

        return response()->json([
            'fee' => $typeFees[$type],
            'type' => $type,
            'type_label' => $typeLabels[$type]
        ]);
    }

    public function newServiceStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'billing_address_id' => 'required|exists:additional_addresses,id',
            'shipping_address_id' => 'required|exists:additional_addresses,id',
            'files.*' => 'required|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $selectedDate = $request->date;
        $selectedTime = $request->time;

        if ($selectedDate === now()->format('Y-m-d') && $selectedTime <= now()->format('H:i')) {
            return response()->json([
                'status' => false,
                'message' => 'Ora trebuie să fie după ora curentă pentru astăzi.',
                'errors' => ['time' => ['Ora trebuie să fie după cea curentă.']]
            ], 422);
        }


        $now = now();
        $serviceDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);
        $diffInMinutes = $now->diffInMinutes($serviceDateTime, false);
        $hour = (int) $serviceDateTime->format('H');
        $dayOfWeek = $serviceDateTime->dayOfWeek;

        $company = CompanyDetails::select('opening_time', 'closing_time', 'status')->first();

        if (!$company || $company->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Company is closed. Booking unavailable.'
            ], 403);
        }

        $openingHour = $company->opening_time ?? '09:00';
        $closingHour = $company->closing_time ?? '18:00';

        $opening = (int) Carbon::createFromFormat('H:i', $openingHour)->format('H');
        $closing = (int) Carbon::createFromFormat('H:i', $closingHour)->format('H');

        $typeFees = [1 => 400.00, 2 => 250.00, 3 => 300.00, 4 => 0.00];

        $monthName = $serviceDateTime->format('F');
        $day = $serviceDateTime->day;
        $holiday = Holiday::where('month', $monthName)->where('day', $day)->where('status', true)->first();

        if ($holiday || $dayOfWeek === 0) {
            $type = 3;
        } elseif ($serviceDateTime->isToday() && $diffInMinutes >= 0 && $diffInMinutes <= 120) {
            $type = 1;
        } elseif ($serviceDateTime->isToday() && $diffInMinutes > 120) {
            $type = 2;
        } elseif ($hour < $opening || $hour >= $closing) {
            $type = 3;
        } else {
            $type = 4;
        }

        $additionalFee = $typeFees[$type];

        $booking = ServiceBooking::create([
            'user_id' => auth()->id(),
            'service_id' => null,
            'billing_address_id' => $request->billing_address_id,
            'shipping_address_id' => $request->shipping_address_id,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'additional_fee' => $additionalFee,
            'type' => $type,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/service'), $filename);
                $booking->files()->create(['file' => $filename]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Programarea a fost creată cu succes.',
            'data' => $booking->load('files'),
        ]);
    }

}
