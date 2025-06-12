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

class FrontendController extends Controller
{
    public function index()
    {
        $categories = Category::with('subcategories')->where('status', 1)->get();
        $types = Type::with(['services' => function($q) {
            $q->where('status', 1);
        }])->where('status', 1)->get();
        $companyDetails = CompanyDetails::select('footer_content', 'company_name', 'address1', 'phone1', 'email1')->first();
        return view('frontend.index', compact('categories', 'companyDetails', 'types'));
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
        $request->validate([
            'contactemail' => ['required', 'email'],
            'firstname' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'contactmessage' => ['required'],
        ], [
            'firstname.required' => 'First Name field is required.',
            'lastname.required' => 'Last Name field is required.',
            'contactmessage.required' => 'Message field is required.',
            'contactemail.required' => 'Email field is required.'
        ]);

        $adminmail = Contact::where('id', 1)->first()->email;
        $contactmail = $request->contactemail;
        $ccEmails = $adminmail;
        $msg = $request->contactmessage; 

        if (isset($msg)) {
            $array['firstname'] = $request->firstname; 
            $array['lastname'] = $request->lastname; 
            $array['email'] = $request->contactemail;
            $array['subject'] = "Order Booking Confirmation";
            $array['message'] = $msg;
            $array['contactmail'] = $contactmail;

            Mail::to($ccEmails)
                ->send(new ContactMessageMail($array));
                
                
            Mail::to($adminmail)
                ->send(new ContactMessageMail($array));

            return redirect()->back()->with("message", "Message sent successfully!");
        } else {
            return redirect()->back()->with("error", "Server Error!");
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

    public function serviceBooking($slug, $type = null)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        $shippingAddresses = AdditionalAddress::where('user_id', auth()->user()->id)->where('type', 1)->latest()->get();
        $billingAddresses = AdditionalAddress::where('user_id', auth()->user()->id)->where('type', 2)->latest()->get();
        return view('frontend.service_booking', compact('service','shippingAddresses','billingAddresses','type'));
    }

    public function bookingStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => (!isset($request->selected_type) || $request->selected_type != 1) ? 'required' : 'nullable',
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

        if ($request->selected_type == 1 || empty($request->time)) {
            $serviceDateTime = null;
        } else {
            $serviceDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);
        }

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
        $openingHour = $company?->opening_time ?? '10:00';
        $closingHour = $company?->closing_time ?? '18:00';

        $opening = Carbon::createFromFormat('H:i', $openingHour)->format('H');
        $closing = Carbon::createFromFormat('H:i', $closingHour)->format('H');

        if (isset($request->selected_type)) {
            $type = $request->selected_type;
            $fee = match ($type) {
                1 => 400.00,
                2 => 250.00,
                3 => 300.00,
                default => 0.00,
            };
        } else {
            if ($serviceDateTime && $serviceDateTime->isToday() && $diffInMinutes >= 0 && $diffInMinutes <= 120) {
                $type = 1; $fee = 400.00;
            } elseif ($serviceDateTime && $serviceDateTime->isToday() && $diffInMinutes > 120) {
                $type = 2; $fee = 250.00;
            } elseif ($dayOfWeek === 0 || $hour < $opening || $hour >= $closing) {
                $type = 3; $fee = 300.00;
            } else {
                $type = 4; $fee = 0.00;
            }
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
            'time' => ($request->selected_type == 1) ? null : $request->time,
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

        return redirect()->route('homepage')->with('success', 'Booking created successfully.');
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
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|size:10|regex:/^[0-9]+$/',
            'stars' => 'required|integer|min:1|max:5',
            'review' => 'required|max:1000'
        ], [
            'review.max' => 'The review may not be greater than 1000 characters.',
        ]);

        $validated['status'] = 0;
        Review::create($validated);

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }

    public function showRequestQuoteForm()
    {
        return view('frontend.quote_request');
    }

    public function requestQuote(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|size:10|regex:/^[0-9]+$/',
            'address_first_line' => 'required|string|max:255',
            'address_second_line' => 'nullable|string|max:255',
            'address_third_line' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:400',
            'postcode' => 'nullable|string|max:400',
            'details' => 'required|string|min:10|max:1500',
            'file' => 'nullable|max:10240'
        ]);

        $quote = new Quote();
        $quote->name = $validatedData['name'];
        $quote->email = $validatedData['email'];
        $quote->phone = $validatedData['phone'];
        $quote->address_first_line = $validatedData['address_first_line'];
        $quote->address_second_line = $validatedData['address_second_line'];
        $quote->address_third_line = $validatedData['address_third_line'];
        $quote->postcode = $validatedData['postcode'];
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

        return redirect()->back()->with('success', 'Your quote request has been submitted successfully!');
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|size:10|regex:/^[0-9]+$/',
            'address_first_line' => 'required|string|max:255',
            'address_second_line' => 'nullable|string|max:255',
            'address_third_line' => 'nullable|string|max:255',
            'town' => 'required|string|max:255',
            'postcode' => 'required|string|max:10',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'cv' => 'required|file|mimes:pdf,docx|max:3000',
        ]);

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
        $career->category_ids = json_encode($request->category_ids);
        $career->sub_category_ids = json_encode($request->sub_category_ids);
        $career->created_by = auth()->id();

        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $filename = time() . '_' . rand(100000, 999999) . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/career'), $filename);
            $career->cv = '/images/career/' . $filename;
        }

        $career->save();

        return redirect()->back()->with('success', 'Your data has been submitted successfully!');
    }

    public function callBack(Request $request)
    {
        $callback = new CallBack();
        $callback->user_id = Auth::id();
        $callback->date = Carbon::now()->format('Y-m-d');
        $callback->save();

        if ($callback->exists) {
            $userData = [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone,
                'subject' => 'Callback Request', 
            ];

            $adminEmail = Contact::where('id', 1)->value('email');

            Mail::to($adminEmail)->send(new CallbackMail($userData));

            return redirect()->back()->with('callback_message', 'Callback request sent successfully.');
        } else {
          return redirect()->back()->with('callback_error', 'Failed to request a callback.');
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

}
