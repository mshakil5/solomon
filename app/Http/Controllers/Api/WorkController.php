<?php

namespace App\Http\Controllers\Api;

use App\Models\Work;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Upload;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobOrderMail;
use App\Models\ReviewAnswer;
use App\Models\ReviewQuestion;
use App\Models\WorkReview;
use App\Models\WorkReviewReply;

class WorkController extends Controller
{
    public function userWorks(Request $request)
    {
        $userId = $request->user()->id;
        $data = Work::with('transactions','invoice','workimage')->where('user_id', $userId)->orderBy('id', 'DESC')->get();
        
        if ($data) {
            $success['data'] = $data;
            return response()->json(['success'=>true,'response'=> $success], 200);
        } else {
            $success['data'] = "No data found";
            return response()->json(['success'=>false,'response'=> $success], 202);
        }
    }

    public function workDetails($id, Request $request)
    {
        $work = Work::with('transactions','invoice','workimage')->where('id', $id)->first();
        if ($work && $work->user_id == $request->user()->id) {
            $success['data'] = $work;
            return response()->json(['success' => true, 'response' => $success], 200);
        }else{
             $success['Message'] = 'No data found.';
            return response()->json(['success' => false, 'response' => $success], 202);
        }
    
    }

    public function showInvoiceApi($id, Request $request)
    {
        $work = Work::findOrFail($id);
        if ($work->user_id != $request->user()->id) {
            return response()->json(['success' => false, 'response' => ['Message' => 'No data found.']], 202);
        }
        $invoice = $work->invoice;
        $jobid = $work->orderid;

        if ($invoice) {
            $success['data'] = $invoice;
            $success['jobid'] = $jobid;
            return response()->json(['success' => true, 'response' => $success], 200);
        }else{
            $success['Message'] = 'No data found.';
            return response()->json(['success' => false, 'response' => $success], 202);
        }  
    }

    public function showTransactionsApi($id, Request $request)
    {
        $work = Work::findOrFail($id);
        if ($work->user_id != $request->user()->id) {
            return response()->json(['success' => false, 'response' => ['Message' => 'No data found.']], 202);
        }
        $transactions = $work->transactions;
        if ($transactions){
            $success['data'] = $transactions;
            $success['jobId'] = $work->orderid;
            return response()->json(['success' => true, 'response' => $success], 200);
        }else{
            $success['Message'] = 'No data found.';
            return response()->json(['success' => false, 'response' => $success], 202);
        }
        
    }

    public function getAssignedTasks()
    {
        $tasks = Work::with('workTimes')
                     ->where('status', '2')
                     ->where('assigned_to', Auth::id())
                     ->orderBy('id', 'DESC')
                     ->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No assigned tasks found.'], 404);
        }

        return response()->json(['tasks' => $tasks], 200);
    }

    public function getCompletedTasks()
    {
        $tasks = Work::with('workTimes')
                     ->where('status', '3')
                     ->where('assigned_to', Auth::id())
                     ->orderBy('id', 'DESC')
                     ->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No completed tasks found.'], 404);
        }

        return response()->json(['tasks' => $tasks], 200);
    }

    public function workDetailsByStaff($id, Request $request)
    {
        $work = Work::with('transactions','invoice','workimage')->where('id', $id)->first();
        if ($work && $work->assigned_to == Auth::id()) {
            $success['data'] = $work;
            return response()->json(['success' => true, 'response' => $success], 200);
        }else{
             $success['Message'] = 'No data found.';
            return response()->json(['success' => false, 'response' => $success], 202);
        }
    
    }

    public function changeWorkStatusStaff(Request $request, $work_id)
    {
        $validatedData = $request->validate([
            'status' => 'required|integer|in:2,3',
        ]);

        $work = Work::find($work_id);

        if (!$work) {
            return response()->json(['error' => 'Work not found'], 404);
        }

        $work->status = $validatedData['status'];

        if ($work->save()) {
            $message = "Status changed successfully.";
            return response()->json([
                'status' => 200,
                'message' => $message,
                'work ID' => $work_id
            ], 200);
        } else {
            $message = "There was an error changing status.";
            return response()->json(['status' => 500, 'message' => $message], 500);
        }
    }

    public function completedWorkDetails($id)
    {
        $work = Work::findOrFail($id);
        
        if ($work->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'response' => [
                    'message' => 'Forbidden: You do not have access to this work.'
                ]
            ], 403);
        }

        $uploads = Upload::where('work_id', $id)->get(['image', 'video']);

        if ($uploads->isEmpty()) {
            return response()->json([
                'success' => false,
                'response' => [
                    'message' => 'No uploads found for the given work ID.',
                    'data' => []
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'response' => [
                'message' => 'Uploads retrieved successfully.',
                'data' => $uploads
            ]
        ], 200);
    }

    public function storeWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'address_first_line' => ['required'],
            'post_code' => ['required'],
            'town' => ['nullable'],
            'phone' => ['required', 'regex:/^\d{10}$/'],
            'images.*' => ['required', 'mimes:jpeg,png,jpg,gif,svg,mp4,avi,mov,wmv', 'max:102400'],
            'descriptions.*' => ['required', 'string'],
        ], [
            'phone.regex' => 'The phone number must be exactly 10 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = new Work();
        $data->user_id = Auth::id();
        $data->orderid = mt_rand(100000, 999999);
        $data->date = date('Y-m-d');
        $data->name = $request->name;
        $data->category_id = $request->category_id;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address_first_line = $request->address_first_line;
        $data->address_second_line = $request->address_second_line;
        $data->address_third_line = $request->address_third_line;
        $data->town = $request->town;
        $data->post_code = $request->post_code;
        $data->created_by = Auth::id();
        $data->save();

        $categoryName = $data->category->name;

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $descriptions = $request->input('descriptions');

            foreach ($files as $index => $image) {
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

        $adminMail = Contact::where('id', 1)->first()->email;
        $contactMail = $request->email;
        $msg = "Thank you for telling us about your work.";

        $mailData = [
            'firstname' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address1' => $request->address_first_line,
            'address2' => $request->address_second_line,
            'address3' => $request->address_third_line,
            'town' => $request->town,
            'postcode' => $request->post_code,
            'subject' => "Order Booking Confirmation",
            'message' => $msg,
            'contactmail' => $contactMail,
            'category_name' => $categoryName,
        ];

        Mail::to($contactMail)->send(new JobOrderMail($mailData));
        Mail::to($adminMail)->send(new JobOrderMail($mailData));

        return response()->json([
            'success' => true,
            'message' => 'Your work has been submitted successfully!',
            'data' => $data,
        ], 201);
    }

    public function showReviewForm($id)
    {
        $work = Work::findOrFail($id);
        $questions = ReviewQuestion::where('status', 1)->latest()->get();

        $existingReview = WorkReview::with(['answers.question', 'replies.user'])
            ->where('work_id', $work->id)
            ->where('user_id', auth()->id())
            ->first();

        return response()->json([
            'work' => $work,
            'questions' => $questions,
            'existingReview' => $existingReview,
        ]);
    }

    public function storeReview(Request $request)
    {
        $validatedData = $request->validate([
            'work_id' => 'required|exists:works,id',
            'answers' => 'required|array',
            'note' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999) . '.' . $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/reviews/');
            $uploadedFile->move($destinationPath, $randomName);
            $imageName = $randomName;
        }

        $workReview = WorkReview::create([
            'work_id' => $request->work_id,
            'user_id' => Auth::id(),
            'note' => $request->note,
            'image' => $imageName,
        ]);

        foreach ($request->answers as $questionId => $answer) {
            ReviewAnswer::create([
                'work_review_id' => $workReview->id,
                'review_question_id' => $questionId,
                'answer' => $answer,
            ]);
        }

        return response()->json([
            'message' => 'Review submitted successfully.',
            'data' => $workReview,
        ], 201);
    }

    public function storeReply(Request $request, $reviewId)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);

        $reply = WorkReviewReply::create([
            'work_review_id' => $reviewId,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Reply added successfully!',
            'data' => $reply,
        ], 201);
    }

}
