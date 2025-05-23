<?php

namespace App\Http\Controllers\Api;

use Exception;
use Mail;
use App\Models\Work;
use App\Models\Location;
use App\Models\WorkImage;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use App\Models\Transaction;
use App\Mail\JobOrderMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use App\Models\Career;
use App\Models\Category;
use App\Models\Quote;
use App\Models\CompanyDetails;
use App\Models\Softcode;
use App\Models\Master;
use App\Models\Slider;

class FrontendController extends Controller
{
    public function welcome()
    {
        $welcomeSoftCode = Softcode::where('name', 'welcome')->first();
        if ($welcomeSoftCode) {
            $welcome = Master::where('softcode_id', $welcomeSoftCode->id)->first();
        } else {
            $welcome = null;
        }

        if ($welcome) {
      
            return response()->json([
              'success' => true,
              'response' => [
                  'name' => $welcome->name,
                  'short_title' => $welcome->short_title,
                  'short_description' => $welcome->short_description,
                  'long_description' => $welcome->long_description,
                  'image' => url('images/meta_image/' . $welcome->meta_image)
              ]
          ], 200);

        }else{
            return response()->json([
                'success' => false,
                'message' => 'No data found.'
            ], 404);
        }
    }

    public function slider()
    {
        $slider = Slider::where('status', 1)->latest()->select('image')->get();
    
        if ($slider->isNotEmpty()) {
            $slider = $slider->map(function ($item) {
                $item->image = url('images/slider/' . $item->image);
                return $item;
            });
    
            return response()->json([
                'success' => true,
                'response' => [
                    'slider' => $slider
                ]
            ], 200);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'No data found.'
        ]);
    }    
    
    public function checkPostCode(Request $request)
    {

        $searchdata = substr($request->postcode, 0, 3);

        $data = Location::where('postcode', 'like', '%'.$request->postcode.'%')->orWhere('postcode', 'like', '%'.$searchdata.'%')->first();

        if (isset($data) ) {
            $message ="Available";
            return response()->json(['status'=> 300,'data'=>$data,'message'=>$message]);
        } else {
            $message ="This location is out of our service.";
            return response()->json(['status'=> 303,'message'=>$message]);
        }
        
    }

    public function getCategory()
    {
        $data = Category::with('subcategories')->orderby('id', 'DESC')->get();
        if ($data){
            $success['data'] = $data;
            return response()->json(['success' => true, 'response' => $success], 200);
        }else{
            $success['Message'] = 'No data found.';
            return response()->json(['success' => false, 'response' => $success], 202);
        }
        
    }

    public function workStore2(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'address_first_line' => ['required'],
            'post_code' => ['required'],
            'town' => ['nullable'],
            'phone' => ['required'],
            'images.*' => ['required', 'image'],
        ]);

        $data = new Work();
        $data->user_id = auth()->id();
        $data->orderid = mt_rand(100000, 999999);
        $data->date = date('Y-m-d');
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address_first_line = $request->address_first_line;
        $data->address_second_line = $request->address_second_line;
        $data->address_third_line = $request->address_third_line;
        $data->town = $request->town;
        $data->post_code = $request->post_code;
        $data->created_by = Auth::id();
        $data->save();

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            
            foreach ($files as $index => $image) {
                $validatedData = $request->validate([
                    'images.' . $index => ['required', 'image'],
                ]);

                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $storagePath = public_path('images/works');
                $image->move($storagePath, $filename);

                $workImg = new WorkImage();
                $workImg->work_id = $data->id;
                $workImg->name = 'images/works/' . $filename;
                $workImg->save();
            }
        }
        return response()->json(['message' => 'Work stored successfully.', 'work' => $data], 200);
    }
    
    
    public function workStore(Request $request, $catId = null)
    {
        $rules = [
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'address_first_line' => ['required'],
            'post_code' => ['required'],
            'town' => ['nullable'],
            'phone' => ['required']
        ];

        if ($request->use_different_address == 1) {
            $rules['different_address_first_line'] = ['required'];
            $rules['different_town'] = ['required'];
            $rules['different_post_code'] = ['required'];
        }

        $request->validate($rules);

        $data = new Work();
        $data->user_id = auth()->id();
        $data->orderid = mt_rand(100000, 999999);
        $data->date = date('Y-m-d');
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address_first_line = $request->address_first_line;
        $data->address_second_line = $request->address_second_line;
        $data->address_third_line = $request->address_third_line;
        $data->town = $request->town;
        $data->post_code = $request->post_code;
        // $data->created_by = Auth::id();
        $data->category_id = $catId ?? $request->category_id;
        $data->sub_category_id = $request->sub_category_id;
        $data->use_different_address = $request->use_different_address;

          if ($request->use_different_address == 1) {
            $data->different_address_first_line = $request->different_address_first_line;
            $data->different_address_second_line = $request->different_address_second_line;
            $data->different_address_third_line = $request->different_address_third_line;
            $data->different_town = $request->different_town;
            $data->different_post_code = $request->different_post_code;
        }

        $data->save();

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $descriptions = $request->input('descriptions');
            
            foreach ($files as $index => $image) {
                // $validatedData = $request->validate([
                //     'images.' . $index => ['required', 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,wmv', 'max:102400'],
                //     'descriptions.' . $index => ['required', 'string'],
                // ]);

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
        $category = Category::find($catId);
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
        $array['category_name'] = $category->name;
        
        
        Mail::to($contactmail)
        ->send(new JobOrderMail($array));

        Mail::to($ccEmails)
        ->send(new JobOrderMail($array));
        
        return response()->json(['message' => 'Work stored successfully.', 'work' => $data], 200);
        
    }

    public function workUpdate(Request $request, $id)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'address_first_line' => ['required'],
            'post_code' => ['required'],
            'town' => ['nullable'],
            'phone' => ['required'],
            // 'images.*' => ['required', 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,wmv', 'max:102400'],
            // 'descriptions.*' => ['required', 'string'],
        ]);

        $data = Work::findOrFail($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address_first_line = $request->address_first_line;
        $data->address_second_line = $request->address_second_line;
        $data->address_third_line = $request->address_third_line;
        $data->town = $request->town;
        $data->post_code = $request->post_code;
        $data->save();

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $descriptions = $request->input('descriptions');

            foreach ($files as $index => $image) {
                // $validatedData = $request->validate([
                //     'images.' . $index => ['required', 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,wmv', 'max:102400'],
                //     'descriptions.' . $index => ['required', 'string'],
                // ]);

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
        return response()->json(['message' => 'Work updated successfully.', 'work' => $data], 200);
    }

    public function deleteWork($id)
    {
        $work = Work::find($id);
        if (!$work) {
            return response()->json(['message' => 'Work not found.'], 404);
        }
        if ($work->invoice()->exists()) {
            return response()->json(['message' => 'Cannot delete work. There are associated invoices.'], 422);
        }
        if ($work->delete()) {
            return response()->json(['message' => 'Work deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'Error deleting work.'], 500);
        }
    }
    
    public function getAllTransaction()
    {
        $data = Transaction::where('user_id', Auth::user()->id)->get();
        $works = Work::with('transactions')->where('user_id', Auth::user()->id)->get();
        if ($data){
            $success['data'] = $data;
            $success['works'] = $works;
            return response()->json(['success' => true, 'response' => $success], 200);
        }else{
            $success['Message'] = 'No data found.';
            return response()->json(['success' => false, 'response' => $success], 202);
        }
        
    }

    public function reviewStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|regex:/^[0-9]+$/',
            'stars' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
            'work_id' => 'nullable|string|max:1000',
        ], [
            'review.max' => 'The review may not be greater than 1000 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['status'] = 0;

        $validatedData['user_id'] = Auth::user()->id ?? null;
        $review = Review::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully!',
            'data' => $review,
        ], 201);
    }

    public function joinUsStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|size:10|regex:/^[0-9]+$/',
            'address_first_line' => 'nullable|string|max:255',
            'address_second_line' => 'nullable|string|max:255',
            'address_third_line' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:10',
            'note' => 'nullable',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'cv' => 'required|file|mimes:pdf,docx|max:3000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $career = new Career();
        $career->name = $validatedData['name'] ?? null;
        $career->first_name = $validatedData['first_name'] ?? null;
        $career->email = $validatedData['email'] ?? null;
        $career->phone = $validatedData['phone'] ?? null;
        $career->address_first_line = $validatedData['address_first_line'] ?? null;
        $career->address_second_line = $validatedData['address_second_line'] ?? null;
        $career->address_third_line = $validatedData['address_third_line'] ?? null;
        $career->town = $validatedData['town'] ?? null;
        $career->postcode = $validatedData['postcode'] ?? null; 
        $career->note = $validatedData['note'] ?? null;
        // $career->category_ids = json_encode($validatedData['category_ids']) ?? null;
        $career->created_by = auth()->id() ?? null;

        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $filename = time() . '_' . rand(100000, 999999) . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/career'), $filename);
            $career->cv = '/images/career/' . $filename;
        }

        $career->save();

        return response()->json([
            'success' => true,
            'message' => 'Your data has been submitted successfully!',
            'data' => $career,
        ], 201);
    }

    public function requestQuoteStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();
        $quote = Quote::create($validatedData);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . rand(100000, 999999) . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/quotes'), $filename);
            $quote->file = '/images/quotes/' . $filename;
            $quote->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Your quote request has been submitted successfully!',
            'data' => $quote,
        ], 201);
    }

    public function aboutUs()
    {
        $aboutUs = CompanyDetails::select('about_us')->first()->about_us;
        return response()->json([
            'about_us' => $aboutUs
        ], 200);
    }

    public function privacyPolicy()
    {
        $privacyPolicy = CompanyDetails::select('privacy_policy')->first()->privacy_policy;
        return response()->json([
            'about_us' => $privacyPolicy
        ], 200);
    }

    public function contactUs(Request $request)
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

                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully!',
                ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Server error!',
            ], 401);
        }


    }

    public function getInTouch()
    {
        $companyDetails = CompanyDetails::select('company_logo', 'address1', 'phone1', 'email1')->first();

        if (!$companyDetails) {
            return response()->json(['message' => 'Company details not found'], 404);
        }

        return response()->json([
            'company_logo' => url('images/company/' . $companyDetails->company_logo),
            'address1'     => $companyDetails->address1,
            'phone1'       => $companyDetails->phone1,
            'email1'       => $companyDetails->email1,
        ], 200);
    }

}
