<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Work;
use App\Models\WorkTime;
use App\Models\WorkImage;
use Illuminate\Http\Request;
use App\Models\Upload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\WorkAssignedMail;
use App\Models\WorkAssign;
use App\Models\ReviewQuestion;
use App\Models\WorkReview;
use App\Models\ReviewAnswer;

class WorkController extends Controller
{
    public function index()
    {
        $data = Work::orderby('id','DESC')->get();
        return view('admin.work.index', compact('data'));
    }
    
    public function new()
    {
        $data = Work::orderby('id','DESC')->where('status','1')->get();
        $staffs = User::orderby('id','DESC')->where('is_type','2')->get();
        return view('admin.work.new', compact('data','staffs'));
    }

    public function processing()
    {
        $data = Work::orderby('id','DESC')->where('status','2')->get();
        return view('admin.work.processing', compact('data'));
    }

    public function complete()
    {
        $data = Work::with('invoice')->orderby('id','DESC')->where('status','3')->get();
        return view('admin.work.complete', compact('data'));
    }

    public function cancel()
    {
        $data = Work::orderby('id','DESC')->where('status','4')->get();
        return view('admin.work.cancel', compact('data'));
    }

    public function workGallery($id)
    {
        $data = WorkImage::where('work_id', $id)->orderby('id','DESC')->get();
        return view('admin.work.gallery', compact('data'));
    }

    public function userWorks()
    {
        $userId = auth()->id();
        $works = Work::where('user_id', $userId)->orWhere('email', Auth::user()->email)->orderBy('id', 'DESC')->get();
        return view('user.works', compact('works'));
    }

    public function showTransactions(Work $work)
    {
        $transactions = $work->transactions;
        return view('user.transactions', compact('transactions', 'work'));
    }

    public function workDetailsByAdmin($id)
    {
        $work = Work::where('id', $id)->first();
        return view('admin.work.work_details', compact('work'));
    }

    public function workDetailsByUser($id)
    {

        $uploads = Upload::where('work_id', $id)
                     ->orderBy('id', 'desc')
                     ->get();
        return view('user.work_images', compact('uploads'));
    }

    public function editWork($id)
    {
        $work = Work::with('workimage')->where('id', $id)->first();
        return view('user.work_edit', compact('work'));
    }

    public function showDetails($id)
    {
        $work = Work::with('workimage','category')->where('id', $id)->first();
        return view('user.show_work_details', compact('work'));
    }

    public function workUpdate(Request $request)
    {
        $imgdesc = $request->descriptions;
        $images = $request->images;
        $workimageid = $request->workimageid;

        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'address_first_line' => ['required'],
            'post_code' => ['required'],
            'town' => ['required'],
            'phone' => ['required'],
            'images.*' => ['image', 'nullable'],
            'descriptions.*' => ['nullable', 'string'],
        ]);

        $work = Work::findOrFail($request->workid);

        $work->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'post_code' => $request->post_code,
            'town' => $request->town,
            'address_first_line' => $request->address_first_line,
            'address_second_line' => $request->address_second_line,
            'address_third_line' => $request->address_third_line,
        ]);

        $existingImages = $work->workimage()->get();

        foreach ($existingImages as $existingImage) {
            if (!in_array($existingImage->id, $workimageid)) {
                if (file_exists(public_path('images/works/' . $existingImage->name))) {
                    unlink(public_path('images/works/' . $existingImage->name));
                }
                $existingImage->delete();
            }
        }

        foreach ($imgdesc as $key => $item) {
            $workImg = WorkImage::find($workimageid[$key]);

            if ($workImg) {
                if ($request->hasFile('images.' . $key)) {
                    if (file_exists(public_path('images/works/' . $workImg->name))) {
                        unlink(public_path('images/works/' . $workImg->name));
                    }

                    $file = $request->file('images.' . $key);
                    $rand = mt_rand(100000, 999999);
                    $imageName = time() . $rand . '.' . $file->extension();
                    $file->move(public_path('images/works'), $imageName);
                    $workImg->name = 'images/works/' . $imageName;
                }
                $workImg->description = $item;
                $workImg->save();
            } else {
                if ($request->hasFile('images.' . $key)) {
                    $file = $request->file('images.' . $key);
                    $rand = mt_rand(100000, 999999);
                    $imageName = time() . $rand . '.' . $file->extension();
                    $file->move(public_path('images/works'), $imageName);

                    WorkImage::create([
                        'work_id' => $work->id,
                        'name' => 'images/works/' . $imageName,
                        'description' => $item,
                    ]);
                }
            }
        }
        return redirect()->route("user.works")->with("message", "Updated Successfully");
    }

    public function destroy($id)
    {
        $work = Work::with('workimage')->find($id);

        if ($work) {
            foreach ($work->workimage as $image) {
                $imagePath = public_path($image->name);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }

            $work->delete();

            return redirect()->route('user.works')->with('success', 'Work deleted successfully.');
        }

        return redirect()->route('user.works')->with('error', 'Work not found.');
    }

    public function changeWorkStatus(Request $request)
    {
        $work = Work::find($request->id);
        $work->status = $request->status;

        if ($work->save()) {
            if ($work->status == 1) {
                $stsval = "New";
            } elseif ($work->status == 2) {
                $stsval = "In Progress";
            } elseif ($work->status == 3) {
                $stsval = "Completed";
            } elseif ($work->status == 4) {
                $stsval = "Cancelled";
            }

            $message = "Status Change Successfully.";
            return response()->json(['status' => 300, 'message' => $message, 'stsval' => $stsval, 'id' => $request->id]);
        } else {
            $message = "There was an error to change status!!.";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    }

    public function assignStaff(Request $request)
    {
        //Validation
        $request->validate([
            'work_id' => 'required|exists:works,id',
            'staff_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'note' => 'nullable|string',
        ]);

        $workId = $request->input('work_id');
        $staffId = $request->input('staff_id');

        $work = Work::find($workId);

        if (!$work) {
            return response()->json(['error' => 'Work item not found'], 404);
        }

        //Assign Staff
        WorkAssign::create([
            'work_id' => $workId,
            'staff_id' => $staffId,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'note' => $request->input('note'),
        ]);

        //Status Update
        $work->status = 2;
        $work->is_new = 0;
        $work->save();

        $staff = User::find($staffId);
        $contactmail = $staff->email;

        $msg = "You have been assigned a new work.";

        $emailData = [
            'staffname' => $staff->name,
            'firstname' => $work->name,
            'email' => $work->email,
            'phone' => $work->phone,
            'address1' => $work->address_first_line,
            'address2' => $work->address_second_line,
            'address3' => $work->address_third_line,
            'town' => $work->town,
            'postcode' => $work->post_code,
            'subject' => "Work Assign",
            'message' => $msg,
            'contactmail' => $contactmail,
        ];

        Mail::to($contactmail)->send(new WorkAssignedMail($emailData));

        return response()->json(['success' => 'Staff assigned successfully']);
    }

    public function getAssignedTasks()
    {
        $data = WorkAssign::with(['work', 'work.workTimes'])
                      ->where('staff_id', auth()->id())
                      ->whereHas('work', function($query) {
                          $query->where('status', 2);
                      })
                      ->orderBy('id', 'DESC')
                      ->get();
        return view('staff.assigned_tasks', compact('data'));
    }

    public function getCompletedTasks()
    {
        $data = WorkAssign::with('work')
            ->where('staff_id', auth()->id())
            ->whereHas('work', function ($query) {
                $query->where('status', '3');
            })
            ->orderBy('id', 'DESC')
            ->get();

        return view('staff.completed_tasks', compact('data'));
    }

    public function workDetailsByStaff($id)
    {
        $work = Work::where('id', $id)->first();
        return view('staff.work_details', compact('work'));
    }

    public function workDetailsUploadByStaff($id)
    {
        $work = Work::where('id', $id)->first();
        return view('staff.work_image', compact('work'));
    }

    public function changeWorkStatusStaff(Request $request)
    {
        $work = Work::find($request->id);

        $work->status = $request->status;

        if ($work->save()) {
            if ($work->status == 1) {
                $stsval = "New";
            } elseif ($work->status == 2) {
                $stsval = "In Progress";
            } elseif ($work->status == 3) {
                $stsval = "Completed";
            } elseif ($work->status == 4) {
                $stsval = "Cancelled";
            }

            $message = "Status Change Successfully.";
            return response()->json(['status' => 300, 'message' => $message, 'stsval' => $stsval, 'id' => $request->id]);
        } else {
            $message = "There was an error to change status!!.";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    }


    public function workImageUploadByStaff(Request $request)
    {
        $image = $request->image;
        $work_id = $request->work_id;
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:20480', // 20MB Max
        ]);

        // $workImg = WorkImage::find($workimageid[$key]);
        // if ($request->hasFile('images')) {
        //     $files = $request->file('images');
        //     $rand = mt_rand(100000, 999999);
        //     $imageName = time() . $rand . '.' . $files[$key]->extension();
        //     $files[$key]->move(public_path('images'), $imageName);
        //     $workImg->name = $imageName;
        // }
        // $workImg->description = $descriptions[$key] ?? null;
        // $workImg->save();
        return back()->with("message", "Updated Successfully");
    }

    public function uploadPage($id) 
    {
        $uploads = Upload::where('work_id', $id)
                     ->orderBy('id', 'desc')
                     ->get();
        return view('staff.upload_image', compact('id', 'uploads'));
    }

    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|mimes:jpeg,png,jpg,gif|max:10240',
            'video' => 'nullable|mimes:mp4,mov|max:102400',
            'work_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status' => 422, 'errors' => $errors], 422);
        }

        $workId = $request->get('work_id');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            $imagePath = 'images/completed_tasks/images/'. $imageName;
            $image->move(public_path('images/completed_tasks/images'), $imageName);
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = time(). '.'. $video->getClientOriginalExtension();
            $videoPath = 'images/completed_tasks/videos/'. $videoName;
            $video->move(public_path('images/completed_tasks/videos'), $videoName);
        }

        $upload = new Upload;
        $upload->work_id = $workId;
        $upload->staff_id = auth()->user()->id;
        $upload->image = $imagePath;
        if ($request->hasFile('video')) {
            $upload->video = $videoPath;
        }
        $upload->created_by = auth()->user()->id;
        if ($upload->save()) {
            return response()->json(['status' => 200, 'message' => 'Uploaded Successfully.']);
        } else {
            return response()->json(['status' => 422, 'errors' => "Server error!!"], 422);
        }
        

    }

    public function deleteFile($id)
    {
        $upload = Upload::find($id);

        if (!$upload) {
            return response()->json(['error' => 'Upload not found'], 404);
        }

        if ($upload->image && file_exists(public_path($upload->image))) {
            unlink(public_path($upload->image));
        }

        if ($upload->video && file_exists(public_path($upload->video))) {
            unlink(public_path($upload->video));
        }

        $upload->delete();

        return response()->json(['success' => 'Upload deleted successfully'], 200);
    }

    public function viewImage($id) 
    {
        $uploads = Upload::where('work_id', $id)
                     ->orderBy('id', 'desc')
                     ->get();
        return view('admin.work.completed_image', compact('uploads'));
    }


    public function showReviewForm($id)
    {
        $work = Work::findOrFail($id);
        $questions = ReviewQuestion::where('status', 1)->latest()->get();

        $existingReview = WorkReview::with('answers.question')
            ->where('work_id', $work->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('user.work_review', compact('work', 'questions', 'existingReview'));
    }

    public function storeReview(Request $request)
    {
        $request->validate([
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

        return redirect()->route('user.works')->with('success', 'Review submitted successfully.');
    }
}
