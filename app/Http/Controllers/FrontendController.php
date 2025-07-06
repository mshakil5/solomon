<?php

namespace App\Http\Controllers;

use mt;
use Mail;
use Exception;
use App\Models\Work;
use App\Models\Contact;
use App\Models\Location;
use App\Models\WorkImage;
use Illuminate\Http\Request;
use App\Mail\ContactMessageMail;
use App\Mail\JobOrderMail;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyDetails;
use App\Models\Review;
use App\Models\Quote;
use Illuminate\Support\Facades\Validator;
use App\Models\Career;
use App\Models\CallBack;
use Illuminate\Support\Carbon;
use App\Mail\CallbackMail;
use App\Models\Service;
use App\Models\SubCategory;
use App\Models\Type;
use App\Models\AdditionalAddress;
use App\Models\ServiceBooking;
use App\Models\Slider;
use App\Models\Transaction;
use App\Models\NewService;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Payment;
use App\Models\Invoice;
use App\Mail\PaymentSuccessUser;
use App\Models\Holiday;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    public function index()
    {
        $categories = Category::with('subcategories')->where('status', 1)->get();
        $types = Type::with(['services' => function($q) {
            $q->where('status', 1);
        }])->where('status', 1)->get();
        $companyDetails = CompanyDetails::select('footer_content', 'company_name', 'address1', 'phone1', 'email1')->first();
        $sliders = Slider::where('status', 1)->orderBy('id', 'desc')->get();
        return view('frontend.index', compact('categories', 'companyDetails', 'types', 'sliders'));
    }

    public function privacy()
    {
        $privacy = CompanyDetails::select('privacy_policy')->first()->privacy_policy;
        return view('frontend.privacy', compact('privacy'));
    }

    public function terms()
    {
        return view('frontend.terms');
    }

    public function workStore(Request $request)
    {

          $rules = [
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'category_id' => ['required'],
            'sub_category_id' => ['nullable'],
            'address_first_line' => ['required'],
            'post_code' => ['required'],
            'town' => ['nullable'],
            'phone' => ['required', 'regex:/^\d{10}$/'],
            'images.*' => ['nullable', 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,wmv', 'max:102400'],
            'descriptions.*' => ['nullable', 'string'],
        ];

        if ($request->use_different_address == 1) {
            $rules['different_address_first_line'] = ['required'];
            $rules['different_town'] = ['required'];
            $rules['different_post_code'] = ['required'];
        }
        
        $request->validate($rules, [
            'phone.regex' => 'The phone number must be exactly 10 digits.',
        ]);
                
        $data = new Work();
        $data->user_id = auth()->id();
        $data->orderid = mt_rand(100000, 999999);
        $data->date = date('Y-m-d');
        $data->name = $request->name;
        $data->category_id = $request->category_id;
        $data->sub_category_id = $request->sub_category_id;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address_first_line = $request->address_first_line;
        $data->address_second_line = $request->address_second_line;
        $data->address_third_line = $request->address_third_line;
        $data->town = $request->town;
        $data->post_code = $request->post_code;
        $data->created_by = Auth::id();
        $data->use_different_address = $request->use_different_address;

        if ($request->use_different_address == 1) {
            $data->different_address_first_line = $request->different_address_first_line;
            $data->different_address_second_line = $request->different_address_second_line;
            $data->different_address_third_line = $request->different_address_third_line;
            $data->different_town = $request->different_town;
            $data->different_post_code = $request->different_post_code;
        }

        $data->save();

        $categoryName = $data->category->name;

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $descriptions = $request->input('descriptions');

            foreach ($files as $index => $image) {
                $validatedData = $request->validate([
                    'images.' . $index => ['required', 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,wmv', 'max:102400'],
                    'descriptions.' . $index => ['required', 'string'],
                ]);

                $filename = uniqid() . '.' . $image->getClientOriginalExtension();

                $storagePath = public_path('images/works');
                $image->move($storagePath, $filename);

                $workImg = new WorkImage();
                $workImg->work_id = $data->id;
                $workImg->name = 'images/works/' . $filename;
                $workImg->description = $descriptions[$index] ?? null; 
                $workImg->save();
            }
        }
        
        
        $adminmail = Contact::where('id', 1)->first()->email;
        $contactmail = $request->email;
        $ccEmails = $adminmail;
        $msg = "Thank you for telling us about your work.";

        
        $array['firstname'] = $request->name;
        $array['email'] = $request->email;
        $array['phone'] = $request->phone;
        $array['address1'] = $request->address_first_line;
        $array['address2'] = $request->address_second_line;
        $array['address3'] = $request->address_third_line;
        $array['town'] = $request->town;
        $array['postcode'] = $request->post_code;
        $array['subject'] = "Order Booking Confirmation";
        $array['message'] = $msg;
        $array['contactmail'] = $contactmail;
        $array['category_name'] = $categoryName;
        
        Mail::to($contactmail)
        ->send(new JobOrderMail($array));

        Mail::to($ccEmails)
        ->send(new JobOrderMail($array));
        
        return redirect()->route('homepage')->with("success", "Thank you for telling us about your work");
    }

    public function checkPostCode(Request $request)
    {

        $searchdata = substr($request->postcode, 0, 3);

        $data = Location::where('postcode', 'like', '%'.$request->postcode.'%')->orWhere('postcode', 'like', '%'.$searchdata.'%')->first();

        if (isset($data) ) {
            $message ="<b style='color: green'>Available</b>";
            return response()->json(['status'=> 300,'data'=>$data,'message'=>$message]);
        } else {
            $message ="<b style='color: red'>This location is out of our service.</b>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }
        

    }

    public function showContactForm()
    {
        return view('frontend.contact');
    }

    public function contactMessage(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'contactemail.required' => 'Câmpul Email este obligatoriu.',
            'contactemail.email' => 'Email-ul trebuie să fie valid.',
            'firstname.required' => 'Câmpul Prenume este obligatoriu.',
            'lastname.required' => 'Câmpul Nume este obligatoriu.',
            'contactmessage.required' => 'Câmpul Mesaj este obligatoriu.',
        ] : [
            'contactemail.required' => 'Email field is required.',
            'contactemail.email' => 'Email must be valid.',
            'firstname.required' => 'First Name field is required.',
            'lastname.required' => 'Last Name field is required.',
            'contactmessage.required' => 'Message field is required.',
        ];

        $request->validate([
            'contactemail' => ['required', 'email'],
            'firstname' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'contactmessage' => ['required'],
        ], $messages);

        $adminmail = Contact::where('id', 1)->value('email');
        $contactmail = $request->contactemail;
        $msg = $request->contactmessage;

        if ($msg) {
            $array = [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $contactmail,
                'subject' => $lang == 'ro' ? 'Confirmarea comenzii' : 'Order Booking Confirmation',
                'message' => $msg,
                'contactmail' => $contactmail,
            ];

            Mail::to($adminmail)->send(new ContactMessageMail($array));

            $successMsg = $lang == 'ro' 
                ? 'Mesajul a fost trimis cu succes!' 
                : 'Message sent successfully!';

            return redirect()->back()->with('success', $successMsg);
        } else {
            $errorMsg = $lang == 'ro' 
                ? 'Eroare server!' 
                : 'Server Error!';

            return redirect()->back()->with('error', $errorMsg);
        }
    }

    public function showCategoryDetails($category, $subcategory = null)
    {
        
        $category = Category::where('slug', $category)->select('id', 'name')->firstOrFail();

        if ($subcategory) {
            $subcategory = SubCategory::where('slug', $subcategory)->select('id', 'name')->firstOrFail();
        } else {
            $subcategory = null;
        }
        

        $companyDetails = CompanyDetails::select('footer_content')->first();
        return view('frontend.post_job', compact('category', 'companyDetails','subcategory'));
    }

    public function serviceBooking($slug=null, $type = null)
    {
        if (!auth()->check()) {
            abort(403);
        }
        $description = null;
        $service = null;

          if ($slug !== null) {
              $service = Service::where('slug', $slug)->firstOrFail();
          } else {
              $description = request('need') ?? 'Requested Service';
          }
        $shippingAddresses = AdditionalAddress::where('user_id', auth()->user()->id)->latest()->get();
        $billingAddresses = AdditionalAddress::where('user_id', auth()->user()->id)->latest()->get();


        return view('frontend.service_booking', compact('service','shippingAddresses','billingAddresses','type','description'));
    }

    public function calculateFee_old(Request $request)
    {

        $date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
        $time = $request->time;
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

        $serviceDateTime = $time;

        if ($serviceDateTime) {
            $diffInMinutes = $now->diffInMinutes($serviceDateTime, false);
            $hour = (int)$serviceDateTime->format('H');
            $dayOfWeek = $serviceDateTime->dayOfWeek;
        } else {
            $diffInMinutes = null;
            $hour = null;
            $dayOfWeek = null;
        }

        $company = CompanyDetails::select('opening_time', 'closing_time', 'status')->first();

        if (!$company || $company->status != 1) {
            return response()->json([
                'error' => true,
                'message' => 'Service not available in this time period',
            ]);
        }

        $openingHour = $company?->opening_time ?? '10:00';
        $closingHour = $company?->closing_time ?? '18:00';

        $opening = (int)Carbon::createFromFormat('H:i', $openingHour)->format('H');
        $closing = (int)Carbon::createFromFormat('H:i', $closingHour)->format('H');

        if ($serviceDateTime && $serviceDateTime->isToday() && $diffInMinutes >= 0 && $diffInMinutes <= 120) {
            $type = 1; // Emergency
        } elseif ($serviceDateTime && $serviceDateTime->isToday() && $diffInMinutes > 120 && $hour >= $opening && $hour < $closing) {
            $type = 2; // Prioritized
        } elseif ($serviceDateTime && ($hour < $opening || $hour >= $closing)) {
            $type = 3; // After-hours
        } else {
            $type = 4; // Standard
        }

        return response()->json([
            'fee' => $typeFees[$type],
            'date' => $date,
            'time' => $time,
            'type' => $type,
            'type_label' => $typeLabels[$type]
        ]);
    }

    public function calculateFee(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'date' => 'required|regex:/^\d{2}\/\d{2}\/\d{4}$/',
                'time' => 'required|regex:/^\d{2}:\d{2}:\d{2}$/'
            ]);

            // Parse date (dd/mm/yyyy) and time (HH:mm:ss)
            $date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
            $time = $request->time;
            // Combine date and time into a single Carbon instance
            $serviceDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "$date $time");

            $now = Carbon::now();

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

            $company = CompanyDetails::select('opening_time', 'closing_time', 'status')->first();

            if (!$company || $company->status != 1) {
                return response()->json([
                    'error' => true,
                    'message' => 'Service not available in this time period',
                ]);
            }

            $openingHour = $company->opening_time ?? '10:00';
            $closingHour = $company->closing_time ?? '18:00';

            $opening = (int) Carbon::createFromFormat('H:i', $openingHour)->format('H');
            $closing = (int) Carbon::createFromFormat('H:i', $closingHour)->format('H');

            $hour = (int) $serviceDateTime->format('H');
            $diffInMinutes = $now->diffInMinutes($serviceDateTime, false);
            $dayOfWeek = $serviceDateTime->dayOfWeek;
            $monthName = $serviceDateTime->format('F');
            $day = $serviceDateTime->day;
            $holiday = Holiday::where('month', $monthName)
            ->where('day', $day)
            ->where('status', true)
            ->first();

            if ($dayOfWeek === 0) {
                $type = 3; // Sunday → Outside working hours
            } elseif($holiday) {
                $type = 3; // Holiday → Outside working hours
            } elseif ($serviceDateTime->isToday() && $diffInMinutes >= 0 && $diffInMinutes <= 120) {
                $type = 1; // Emergency
            } elseif ($serviceDateTime->isToday() && $diffInMinutes > 120 && $hour >= $opening && $hour < $closing) {
                $type = 2; // Prioritized
            } elseif ($hour < $opening || $hour >= $closing) {
                $type = 3; // After-hours
            } else {
                $type = 4; // Standard
            }

            return response()->json([
                'fee' => $typeFees[$type],
                'date' => $request->date,
                'time' => substr($time, 0, 5), // Return HH:mm format
                'type' => $type,
                'type_label' => $typeLabels[$type]
            ]);
        } catch (\Exception $e) {
            \Log::error('Calculate fee error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Invalid date or time format',
            ], 422);
        }
    }


    public function bookingStore(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'service_id.exists' => 'Serviciul selectat nu există.',
            'date_time.required' => 'Data și ora sunt obligatorii.',
            'billing_address_id.required' => 'Adresa de facturare este obligatorie.',
            'billing_address_id.exists' => 'Adresa de facturare nu există.',
            'shipping_address_id.required' => 'Adresa de livrare este obligatorie.',
            'shipping_address_id.exists' => 'Adresa de livrare nu există.',
            'files.*.file' => 'Fișierul trebuie să fie valid.',
            'files.*.max' => 'Fișierul nu trebuie să depășească 10MB.',
        ] : [
            'service_id.exists' => 'Selected service does not exist.',
            'date_time.required' => 'Date and time are required.',
            'billing_address_id.required' => 'Billing address is required.',
            'billing_address_id.exists' => 'Billing address does not exist.',
            'shipping_address_id.required' => 'Shipping address is required.',
            'shipping_address_id.exists' => 'Shipping address does not exist.',
            'files.*.file' => 'File must be valid.',
            'files.*.max' => 'File must not exceed 10MB.',
        ];

        $validator = Validator::make($request->all(), [
            'service_id' => 'nullable|exists:services,id',
            'description' => 'nullable|string',
            'date_time' => 'required',
            'billing_address_id' => 'required|exists:additional_addresses,id',
            'shipping_address_id' => 'required|exists:additional_addresses,id',
            'files.*' => 'nullable|file|max:10240',
        ], $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->filled('date_time')) {
            [$datePart, $timePart] = explode(' ', $request->date_time);
            $date = Carbon::createFromFormat('d/m/Y', $datePart)->format('Y-m-d');
            $time = $timePart;
            $request->merge(['date' => $date, 'time' => $time]);
        }

        // Fetch company details & check status
        $company = CompanyDetails::select('opening_time', 'closing_time', 'status')->first();
        if (!$company || $company->status == 0) {
            $errorMsg = $lang == 'ro' ? 'Compania este închisă momentan.' : 'Company is currently closed';
            return back()->withErrors(['company' => $errorMsg])->withInput();
        }

        $openingHour = $company->opening_time ?? '09:00';
        $closingHour = $company->closing_time ?? '18:00';

        $opening = (int) Carbon::createFromFormat('H:i', $openingHour)->format('H');
        $closing = (int) Carbon::createFromFormat('H:i', $closingHour)->format('H');

        $serviceDateTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        $now = now();
        $diffInMinutes = $now->diffInMinutes($serviceDateTime, false);
        $hour = (int) $serviceDateTime->format('H');
        $dayOfWeek = $serviceDateTime->dayOfWeek;

        // Check holiday
        $monthName = $serviceDateTime->format('F');
        $day = $serviceDateTime->day;
        $holiday = Holiday::where('month', $monthName)->where('day', $day)->where('status', true)->first();

        // Fee structure
        $typeFees = [
            1 => 400, // Emergency
            2 => 250, // Prioritized
            3 => 300, // Outside working hours
            4 => 0,   // Standard
        ];

        // Calculate type based on conditions
        if ($holiday || $dayOfWeek === 0) {
            $type = 3; // Holiday or Sunday → Outside working hours
        } elseif ($serviceDateTime->isToday() && $diffInMinutes >= 0 && $diffInMinutes <= 120) {
            $type = 1; // Emergency
        } elseif ($serviceDateTime->isToday() && $diffInMinutes > 120) {
            $type = 2; // Prioritized
        } elseif ($hour < $opening || $hour >= $closing) {
            $type = 3; // Outside working hours
        } else {
            $type = 4; // Standard
        }

        $additionalFee = $typeFees[$type];

        // Handle files upload
        $tempFiles = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('temp_booking_files'), $filename);
                $tempFiles[] = $filename;
            }
        }

        $data = $request->except('_token', 'files');
        $data['type'] = $type;
        $data['additional_fee'] = $additionalFee;
        $data['temp_files'] = $tempFiles;

        if ($additionalFee > 0) {
            session(['booking_request' => $data]);
            return redirect()->route('stripe.booking.pay');
        }

        $successMsg = $lang == 'ro' 
            ? 'Cererea de rezervare a fost trimisă cu succes!' 
            : 'Booking request submitted successfully!';

        return $this->finalizeBooking($data, $type, $additionalFee)
                    ->with('success', $successMsg);
    }

    public function stripeBookingPay()
    {
        $data = session('booking_request');

        if (!$data || !isset($data['additional_fee'])) {
            return redirect()->route('homepage')->with('error', 'Invalid booking session.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'ron',
                    'unit_amount' => $data['additional_fee'] * 100,
                    'product_data' => [
                        'name' => 'Booking Additional Fee',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.booking.success'),
            'cancel_url' => route('stripe.booking.cancel'),
        ]);

        return redirect($session->url);
    }

    public function stripeBookingSuccess()
    {
        $data = session('booking_request');

        if (!$data) {
            return redirect()->route('homepage')->with('error', 'Session expired.');
        }

        $fee = $data['additional_fee'];
        $type = $data['type'];
        $userId = auth()->id();

        $transaction = new Transaction();
        $transaction->date = now()->format('Y-m-d');
        $transaction->user_id = $userId;
        $transaction->amount = $fee;
        $transaction->net_amount = $fee;
        $transaction->tranid = now()->timestamp . $userId;
        $transaction->payment_type = 'Additional Fee';
        $transaction->save();

        $data['transaction_id'] = $transaction->id;

        return $this->finalizeBooking($data, $type, $fee);
    }

    public function stripeBookingCancel()
    {
        return redirect()->route('homepage')->with('error', 'Stripe payment was canceled.');
    }

    public function finalizeBooking(array $data, int $type, float $fee)
    {
        $service = null;
        if (!empty($data['service_id'])) {
            $service = Service::findOrFail($data['service_id']);
        }

        $booking = ServiceBooking::create([
            'user_id' => auth()->id(),
            'service_id' => $data['service_id'] ?? null,
            'billing_address_id' => $data['billing_address_id'],
            'shipping_address_id' => $data['shipping_address_id'],
            'description' => $data['description'],
            'date' => $data['date'],
            'time' => $data['time'],
            'service_fee' => 0,
            'additional_fee' => $fee,
            'total_fee' => $fee,
            'type' => $type
        ]);

        if (!empty($data['temp_files'])) {
            foreach ($data['temp_files'] as $filename) {
                $sourcePath = public_path('temp_booking_files/' . $filename);
                $destinationPath = public_path('images/service/' . $filename);

                if (file_exists($sourcePath)) {
                    rename($sourcePath, $destinationPath);
                    $booking->files()->create(['file' => $filename]);
                }
            }
        }

        if (isset($data['transaction_id'])) {
            Transaction::where('id', $data['transaction_id'])
                ->update(['booking_id' => $booking->id]);
        }

        session()->forget('booking_request');

        return redirect()->route('user.service.bookings')->with('success', 'Booking created successfully.');
    }

    public function payStripe(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric']);

        session(['invoice_id' => $id]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'ron',
                    'unit_amount' => $request->amount * 100,
                    'product_data' => [
                        'name' => "Invoice #$id Payment",
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function stripeSuccess(Request $request)
    {
        $invoice_id = session('invoice_id');
        if (!$invoice_id) {
            return redirect()->route('homepage')->with('error', 'Session expired.');
        }

        $session_id = $request->get('session_id');
        if (!$session_id) {
            return redirect()->route('homepage')->with('error', 'Invalid payment session.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::retrieve($session_id);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('homepage')->with('error', 'Payment not completed.');
            }

            $customer_email = $session->customer_details->email ?? auth()->user()->email;
            $amount = $session->amount_total / 100;

            $user = auth()->user();

            $payment = new Payment();
            $payment->user_id = $user->id;
            $payment->payment_id = $session->id;
            $payment->payer_email = $customer_email;
            $payment->amount = $amount;
            $payment->currency = strtoupper($session->currency);
            $payment->payment_status = 'completed';
            $payment->save();

            $invoice = Invoice::find($invoice_id);
            $transaction = new Transaction();
            $transaction->date = now()->format('Y-m-d');
            $transaction->user_id = $user->id;
            $transaction->invoice_id = $invoice_id;
            $transaction->payment_type = 'Stripe Payment';
            $transaction->booking_id = $invoice->service_booking_id ?? null;
            $transaction->amount = $amount;
            $transaction->net_amount = $amount;
            $transaction->tranid = now()->timestamp . $user->id;
            $transaction->save();

            $invoice = Invoice::find($invoice_id);
            $invoice->status = 0;
            $invoice->save();

            session()->forget('invoice_id');

            // Send email notifications
            $adminEmail = Contact::where('id', 1)->value('email');
            Mail::to($adminEmail)->send(new PaymentSuccessUser($user, $payment));
            Mail::to($customer_email)->send(new PaymentSuccessUser($user, $payment));

            return redirect()->route('user.service.bookings')->with('success', 'Payment successful.');
        } catch (\Exception $e) {
            return redirect()->route('homepage')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    public function stripeCancel()
    {
        return redirect()->route('homepage')->with('error', 'Stripe payment was canceled.');
    }

    public function aboutUs()
    {
        $aboutUs = CompanyDetails::select('about_us')->first()->about_us;
        return view('frontend.about_us', compact('aboutUs'));
    }

    public function review()
    {
        $reviews = Review::orderBy('id', 'desc')->where('status', '1')->select('name', 'stars', 'review')->take(8)->get();
        return view('frontend.review', compact('reviews'));
    }

    public function reviewStore(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'name.required' => 'Numele este obligatoriu.',
            'name.max' => 'Numele nu poate depăși 255 caractere.',
            'email.required' => 'Email-ul este obligatoriu.',
            'email.email' => 'Email-ul trebuie să fie valid.',
            'email.max' => 'Email-ul nu poate depăși 255 caractere.',
            'phone.required' => 'Telefonul este obligatoriu.',
            'phone.size' => 'Telefonul trebuie să aibă exact 10 cifre.',
            'phone.regex' => 'Telefonul trebuie să conțină doar cifre.',
            'stars.required' => 'Rating-ul este obligatoriu.',
            'stars.integer' => 'Rating-ul trebuie să fie un număr întreg.',
            'stars.min' => 'Rating-ul minim este 1.',
            'stars.max' => 'Rating-ul maxim este 5.',
            'review.required' => 'Recenzia este obligatorie.',
            'review.max' => 'Recenzia nu poate depăși 1000 caractere.',
        ] : [
            'review.max' => 'The review may not be greater than 1000 characters.',
        ];

        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|size:10|regex:/^[0-9]+$/',
            'stars' => 'required|integer|min:1|max:5',
            'review' => 'required|max:1000',
        ], $messages);

        $validated['status'] = 0;
        Review::create($validated);

        $successMsg = $lang == 'ro' 
            ? 'Recenzia a fost trimisă cu succes!' 
            : 'Review submitted successfully!';

        return redirect()->back()->with('success', $successMsg);
    }

    public function showRequestQuoteForm()
    {
        return view('frontend.quote_request');
    }

    public function requestQuote(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'name.required' => 'Numele este obligatoriu.',
            'name.string' => 'Numele trebuie să fie text.',
            'name.max' => 'Numele nu poate depăși 255 caractere.',
            'email.required' => 'Email-ul este obligatoriu.',
            'email.email' => 'Email-ul trebuie să fie valid.',
            'email.max' => 'Email-ul nu poate depăși 255 caractere.',
            'phone.required' => 'Telefonul este obligatoriu.',
            'phone.size' => 'Telefonul trebuie să aibă exact 10 cifre.',
            'phone.regex' => 'Telefonul trebuie să conțină doar cifre.',
            'address_first_line.required' => 'Adresa este obligatorie.',
            'address_first_line.string' => 'Adresa trebuie să fie text.',
            'address_first_line.max' => 'Adresa nu poate depăși 255 caractere.',
            'address_second_line.string' => 'Adresa trebuie să fie text.',
            'address_second_line.max' => 'Adresa nu poate depăși 255 caractere.',
            'address_third_line.string' => 'Adresa trebuie să fie text.',
            'address_third_line.max' => 'Adresa nu poate depăși 255 caractere.',
            'town.string' => 'Localitatea trebuie să fie text.',
            'town.max' => 'Localitatea nu poate depăși 400 caractere.',
            'postcode.string' => 'Codul poștal trebuie să fie text.',
            'postcode.max' => 'Codul poștal nu poate depăși 400 caractere.',
            'details.required' => 'Detaliile sunt obligatorii.',
            'details.string' => 'Detaliile trebuie să fie text.',
            'details.min' => 'Detaliile trebuie să aibă cel puțin 10 caractere.',
            'details.max' => 'Detaliile nu pot depăși 1500 caractere.',
            'file.max' => 'Fișierul nu poate depăși 10MB.',
        ] : [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be valid.',
            'email.max' => 'Email may not be greater than 255 characters.',
            'phone.required' => 'Phone is required.',
            'phone.size' => 'Phone must be exactly 10 digits.',
            'phone.regex' => 'Phone must contain only digits.',
            'address_first_line.required' => 'Address is required.',
            'address_first_line.string' => 'Address must be a string.',
            'address_first_line.max' => 'Address may not be greater than 255 characters.',
            'address_second_line.string' => 'Address must be a string.',
            'address_second_line.max' => 'Address may not be greater than 255 characters.',
            'address_third_line.string' => 'Address must be a string.',
            'address_third_line.max' => 'Address may not be greater than 255 characters.',
            'town.string' => 'Town must be a string.',
            'town.max' => 'Town may not be greater than 400 characters.',
            'postcode.string' => 'Postcode must be a string.',
            'postcode.max' => 'Postcode may not be greater than 400 characters.',
            'details.required' => 'Details are required.',
            'details.string' => 'Details must be a string.',
            'details.min' => 'Details must be at least 10 characters.',
            'details.max' => 'Details may not be greater than 1500 characters.',
            'file.max' => 'File size may not exceed 10MB.',
        ];

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'size:10', 'regex:/^[0-9]+$/'],
            'address_first_line' => 'required|string|max:255',
            'address_second_line' => 'nullable|string|max:255',
            'address_third_line' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:400',
            'postcode' => 'nullable|string|max:400',
            'details' => 'required|string|min:10|max:1500',
            'file' => 'nullable|max:10240',
        ], $messages);

        $quote = new Quote();
        $quote->name = $validatedData['name'];
        $quote->email = $validatedData['email'];
        $quote->phone = $validatedData['phone'];
        $quote->address_first_line = $validatedData['address_first_line'];
        $quote->address_second_line = $validatedData['address_second_line'] ?? null;
        $quote->address_third_line = $validatedData['address_third_line'] ?? null;
        $quote->postcode = $validatedData['postcode'] ?? null;
        $quote->town = $validatedData['town'] ?? null;
        $quote->details = $validatedData['details'];
        $quote->save();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . rand(100000, 999999) . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/quotes'), $filename);
            $quote->file = '/images/quotes/' . $filename;
            $quote->save();
        }

        $successMsg = $lang == 'ro' 
            ? 'Cererea ta de ofertă a fost trimisă cu succes!' 
            : 'Your quote request has been submitted successfully!';

        return redirect()->back()->with('success', $successMsg);
    }

    public function checkCity(Request $request)
    {
        $request->validate([
            'city' => 'required|string|min:2',
        ]);

        $city = $request->input('city');
        $location = Location::where('city', $city)->where('status', 1)->first();

        if ($location) {
            return response()->json(['success' => true, 'message' => 'This location is available in our service.']);
        } else {
            return response()->json(['success' => false, 'message' => 'This location is not available in our service.']);
        }
    }

    public function suggestCity(Request $request)
    {
        $request->validate([
            'city' => 'required|string|min:2',
        ]);

        $city = $request->input('city');
        $locations = Location::where('city', 'LIKE', "%{$city}%")
            ->where('status', 1)
            ->pluck('city');
        return response()->json($locations);
    }

    public function joinUs()
    {
        $categories = Category::where('status', 1)
          ->with(['subcategories' => function ($query) {
            $query->select('id', 'name', 'category_id');
          }])
          ->select('id', 'name')->get();
        return view('frontend.join_us', compact('categories'));
    }

    public function joinUsStore(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'name.required' => 'Numele este obligatoriu.',
            'name.string' => 'Numele trebuie să fie text.',
            'name.max' => 'Numele nu poate depăși 255 caractere.',
            'email.required' => 'Email-ul este obligatoriu.',
            'email.email' => 'Email-ul trebuie să fie valid.',
            'email.max' => 'Email-ul nu poate depăși 255 caractere.',
            'phone.required' => 'Telefonul este obligatoriu.',
            'phone.size' => 'Telefonul trebuie să aibă exact 10 cifre.',
            'phone.regex' => 'Telefonul trebuie să conțină doar cifre.',
            'address_first_line.string' => 'Adresa trebuie să fie text.',
            'address_first_line.max' => 'Adresa nu poate depăși 255 caractere.',
            'address_second_line.string' => 'Adresa trebuie să fie text.',
            'address_second_line.max' => 'Adresa nu poate depăși 255 caractere.',
            'address_third_line.string' => 'Adresa trebuie să fie text.',
            'address_third_line.max' => 'Adresa nu poate depăși 255 caractere.',
            'town.string' => 'Localitatea trebuie să fie text.',
            'town.max' => 'Localitatea nu poate depăși 255 caractere.',
            'postcode.string' => 'Codul poștal trebuie să fie text.',
            'postcode.max' => 'Codul poștal nu poate depăși 10 caractere.',
            'cv.required' => 'CV-ul este obligatoriu.',
            'cv.file' => 'CV-ul trebuie să fie un fișier.',
            'cv.mimes' => 'CV-ul trebuie să fie în format pdf sau docx.',
            'cv.max' => 'CV-ul nu poate depăși 3MB.',
        ] : [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be valid.',
            'email.max' => 'Email may not be greater than 255 characters.',
            'phone.required' => 'Phone is required.',
            'phone.size' => 'Phone must be exactly 10 digits.',
            'phone.regex' => 'Phone must contain only digits.',
            'address_first_line.string' => 'Address must be a string.',
            'address_first_line.max' => 'Address may not be greater than 255 characters.',
            'address_second_line.string' => 'Address must be a string.',
            'address_second_line.max' => 'Address may not be greater than 255 characters.',
            'address_third_line.string' => 'Address must be a string.',
            'address_third_line.max' => 'Address may not be greater than 255 characters.',
            'town.string' => 'Town must be a string.',
            'town.max' => 'Town may not be greater than 255 characters.',
            'postcode.string' => 'Postcode must be a string.',
            'postcode.max' => 'Postcode may not be greater than 10 characters.',
            'cv.required' => 'CV is required.',
            'cv.file' => 'CV must be a file.',
            'cv.mimes' => 'CV must be a pdf or docx file.',
            'cv.max' => 'CV size may not exceed 3MB.',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|size:10|regex:/^[0-9]+$/',
            'address_first_line' => 'nullable|string|max:255',
            'address_second_line' => 'nullable|string|max:255',
            'address_third_line' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:10',
            'cv' => 'required|file|mimes:pdf,docx|max:3000',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $career = new Career();
        $career->name = $request->name;
        $career->email = $request->email;
        $career->phone = $request->phone;
        $career->address_first_line = $request->address_first_line;
        $career->address_second_line = $request->address_second_line;
        $career->address_third_line = $request->address_third_line;
        $career->town = $request->town;
        $career->postcode = $request->postcode;
        $career->about = $request->about;
        $career->created_by = auth()->id();

        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $filename = time() . '_' . rand(100000, 999999) . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/career'), $filename);
            $career->cv = '/images/career/' . $filename;
        }

        $career->save();

        $successMsg = $lang == 'ro'
            ? 'Datele tale au fost trimise cu succes!'
            : 'Your data has been submitted successfully!';

        return redirect()->back()->with('success', $successMsg);
    }

    public function callBack(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $callback = new CallBack();
        $callback->user_id = Auth::id();
        $callback->date = Carbon::now()->format('Y-m-d');
        $callback->save();

        if ($callback->exists) {
            $userData = [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone,
                'subject' => $lang == 'ro' ? 'Cerere de apelare' : 'Callback Request',
            ];

            $adminEmail = Contact::where('id', 1)->value('email');

            Mail::to($adminEmail)->send(new CallbackMail($userData));

            $successMsg = $lang == 'ro' 
                ? 'Cererea de apelare a fost trimisă cu succes.' 
                : 'Callback request sent successfully.';

            return redirect()->back()->with('success', $successMsg);
        } else {
            $errorMsg = $lang == 'ro' 
                ? 'Cererea de apelare a eșuat.' 
                : 'Failed to request a callback.';

            return redirect()->back()->with('error', $errorMsg);
        }
    }

    public function storeAdditionalAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'first_line' => 'required|string|max:255',
            'second_line' => 'nullable|string|max:255',
            'third_line' => 'nullable|string|max:255',
            'town' => 'required|string|max:255',
            'post_code' => 'required|string|max:255',
            'floor' => 'nullable|string|max:255',
            'apartment' => 'nullable|string|max:255',
            'type' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'msg' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $address = new AdditionalAddress([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'phone' => $request->phone,
            'district' => $request->district,
            'first_line' => $request->first_line,
            'second_line' => $request->second_line,
            'third_line' => $request->third_line,
            'town' => $request->town,
            'post_code' => $request->post_code,
            'floor' => $request->floor,
            'apartment' => $request->apartment,
            'user_id' => Auth::id(),
            'type' => $request->type
        ]);

        $address->save();

        return response()->json([
            'code' => 201,
            'msg' => 'Address created successfully!',
            'address' => $address
        ], 201);
    }

    public function selectType(Request $request)
    {
      $validated = $request->validate([
        'type' => 'required|in:1,2,3,4',
      ]);

      $types = Type::with(['services' => function ($q) {
        $q->where('status', 1);
      }])->where('status', 1)->get();
      $selectedType = $request->type;
      return view('frontend.services', compact('types', 'selectedType'));
    }

    public function inAfara()
    {   
        $sliders = Slider::where('id', 5)->get();
        return view('frontend.in-afara', compact('sliders'));
    }

    public function newService()
    {
      $sliders = Slider::where('id', 6)->get();
      return view('frontend.new-service', compact('sliders'));
    }

    public function newServiceStore(Request $request)
    {
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'need.required' => 'Câmpul „Detalii serviciu” este obligatoriu.',
            'need.string'   => 'Câmpul „Detalii serviciu” trebuie să fie text.',
            'auth.required' => 'Trebuie să fii autentificat pentru a trimite mesajul.',
        ] : [
            'need.required' => 'The Service Details field is required.',
            'need.string'   => 'The Service Details field must be a string.',
            'auth.required' => 'You must be logged in to submit the message.',
        ];

        if (!Auth::check()) {
            $loginUrl = route('login', ['redirect_to' => route('new.service')]);

            $errorMessage = $lang == 'ro'
                ? 'Trebuie să fii autentificat pentru a trimite mesajul. <a href="' . $loginUrl . '">Autentifică-te aici</a>.'
                : 'You must be logged in to submit the message. <a href="' . $loginUrl . '">Login here</a>.';

            return back()
                ->withErrors(['auth' => $errorMessage])
                ->withInput();
        }

        $request->validate([
            'need' => 'required|string',
        ], $messages);

        return redirect()->route('service.booking')->withInput(['need' => $request->need]);
    }

}
