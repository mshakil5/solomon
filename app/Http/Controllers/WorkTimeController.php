<?php

namespace App\Http\Controllers;

use App\Models\WorkTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WorkTimeController extends Controller
{
    public function startWork(Request $request)
    {
        $workTime = new WorkTime();
        $workTime->service_booking_id = $request->input('work_id');
        $workTime->staff_id = auth()->id();
        $workTime->start_time = Carbon::now()->format('Y-m-d H:i');
        $workTime->start_date = Carbon::today()->format('d-m-Y');
        $workTime->created_by = auth()->id();
        $workTime->save();
        return response()->json(['success' => true]);
    }

    public function stopWork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'work_time_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workTimeId = $request->work_time_id;
        $workTime = WorkTime::find($workTimeId);

        if (!$workTime) {
            return response()->json(['error' => 'WorkTime not found'], 404);
        }

        $startTime = Carbon::parse($workTime->start_time);
        $endTime = Carbon::now()->format('Y-m-d H:i');
        $duration = $startTime->diffInSeconds($endTime);
        $workTime->end_time = $endTime;
        $workTime->duration = $duration;
        $workTime->save();
        return response()->json(['success' => true]);
    }

    public function startWorkByAdmin(Request $request)
    {
        $workTime = new WorkTime();
        $workTime->work_id = $request->input('work_id');
        $workTime->start_time = Carbon::now()->format('Y-m-d H:i');
        $workTime->start_date = Carbon::today()->format('d-m-Y');
        $workTime->created_by = auth()->id();
        $workTime->save();
        return response()->json(['success' => true]);
    }

    public function stopWorkByAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'work_time_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workTimeId = $request->work_time_id;
        $workTime = WorkTime::find($workTimeId);

        if (!$workTime) {
            return response()->json(['error' => 'WorkTime not found'], 404);
        }

        $startTime = Carbon::parse($workTime->start_time);
        $endTime = Carbon::now()->format('Y-m-d H:i');
        $duration = $startTime->diffInSeconds($endTime);
        $workTime->end_time = $endTime;
        $workTime->duration = $duration;
        $workTime->updated_by = auth()->id();
        $workTime->save();
        return response()->json(['success' => true]);
    }

    public function workTimeByAdmin($id)
    {
        $workTimes = Worktime::where('work_id', $id)->get();
        $workId = $id;
        return view('admin.work.timer.index', compact('workTimes', 'workId'));
    }

    public function update(Request $request, $id)
    {
        $workTime = WorkTime::findOrFail($id);
    
        $startTime = Carbon::parse($request->start_time);  // No formatting yet
        $endTime = Carbon::parse($request->end_time);  // No formatting yet

        $duration = $startTime->diffInSeconds($endTime);

        $startTimeFormatted = $startTime->format('Y-m-d H:i');
        $endTimeFormatted = $endTime->format('Y-m-d H:i');

        $workTime->start_time = $startTimeFormatted;
        $workTime->end_time = $endTimeFormatted;
        $workTime->duration = $duration;
        $workTime->is_break = $request->is_break;
        $workTime->save();
    
        return response()->json(['message' => 'Work time updated successfully']);
    }
    
    public function storeWorkTimeByAdmin(Request $request)
    {
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $duration = $startTime->diffInSeconds($endTime);
        
        $workTime = new WorkTime();
        $workTime->work_id = $request->work_id;
        $workTime->start_time = $startTime;
        $workTime->start_date = Carbon::today()->format('d-m-Y');
        $workTime->end_time = $endTime;
        $workTime->duration = $duration;
        $workTime->is_break = $request->is_break;
        $workTime->save();

        return response()->json(['message' => 'Work time added successfully']);
    }

    public function destroy($id)
    {
        $workTime = WorkTime::find($id);

        if (!$workTime) {
            return response()->json([
                'message' => 'Work time not found'
            ], 404);
        }

        $workTime->delete();

        return response()->json([
            'message' => 'Work time deleted successfully'
        ], 200);
    }

    public function startBreak(Request $request)
    {
        $chkProcessingWork = WorkTime::whereNull('end_time')
                                    ->where('staff_id', Auth::user()->id)
                                    ->where('is_break', 0)
                                    ->orderBy('id', 'DESC')
                                    ->first();

        $existingBreak = WorkTime::where('staff_id', Auth::user()->id)
                                ->whereDate('created_at', Carbon::today())
                                ->whereNotNull('start_time')
                                ->whereNull('end_time')
                                ->where('is_break', 1)      
                                ->first();

        if ($existingBreak) {
            return response()->json(['message' => 'You are already on a break.'], 400);
        }

        $workTime = new WorkTime();
        
        $workTime->staff_id = Auth::id();
        $workTime->start_time = Carbon::now();
        $workTime->start_date = Carbon::today()->format('d-m-Y');
        $workTime->is_break = 1;
        $workTime->created_by = Auth::id();

        if(isset($chkProcessingWork)){
            $workTime->work_id = $chkProcessingWork->work_id;
        }

        $workTime->save();
        return response()->json(['message' => 'Break started successfully', 'workTimeId' => $workTime->id], 200);
    }

    public function stopBreak(Request $request)
    {
        
        $workTime = WorkTime::find($request->work_time_id);

        if ($workTime->end_time !== null) {
            return response()->json([
                'success' => false,
                'message' => 'You have already stopped your break'
            ], 400);
        }

        if ($workTime) {
            $startTime = $workTime->start_time;
            $endTime = Carbon::now();
            $duration = $endTime->diffInSeconds($startTime);
            $workTime->end_time = $endTime;
            $workTime->duration = $duration;
            $workTime->save();
            return response()->json(['success' => true, 'message' => 'Break Out successful']);
        } else {
            return response()->json(['success' => false, 'message' => 'WorkTime not found'], 404);
        }
    
    }

    public function checkBreak()
    {
        $existingBreak = WorkTime::where('staff_id', Auth::id())
                                ->whereDate('created_at', Carbon::today())
                                ->whereNotNull('start_time')
                                ->whereNull('end_time')
                                ->where('is_break', 1)
                                ->first();

        if ($existingBreak) {
            return response()->json([
                'in_break' => true,
                'workTimeId' => $existingBreak->id,
            ]);
        } else {
            return response()->json([
                'in_break' => false,
                'workTimeId' => null,
            ]);
        }
    }
}
