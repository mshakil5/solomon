<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function getHoliday()
    {
        $data = Holiday::orderBy('month')->orderBy('day')->get();
        return view('admin.holiday.index', compact('data'));
    }

    public function holidayStore(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'holiday_date' => 'required|string',
        ]);

        [$month, $day] = explode(' ', $request->holiday_date);
        $day = (int) $day;

        $existing = Holiday::where('month', $month)
                          ->where('day', $day)
                          ->first();

        if ($existing) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This date is already marked as a holiday.</b></div>";
            return response()->json(['status'=> 303, 'message'=> $message]);
        }

        $data = new Holiday;
        $data->title = $request->title;
        $data->month = $month;
        $data->day = $day;
        $data->created_by = auth()->id();

        if ($data->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Holiday Created Successfully.</b></div>";
            return response()->json(['status'=> 300, 'message'=> $message]);
        } else {
            return response()->json(['status'=> 303, 'message'=> 'Server Error!!']);
        }
    }

    public function holidayEdit($id)
    {
        $where = ['id' => $id];
        $info = Holiday::where($where)->first();
        return response()->json($info);
    }

    public function holidayUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'holiday_date' => 'required|string',
        ]);

        [$month, $day] = explode(' ', $request->holiday_date);
        $day = (int) $day;

        $holiday = Holiday::find($request->codeid);

        if ($holiday->month != $month || $holiday->day != $day) {
            $existing = Holiday::where('month', $month)
                              ->where('day', $day)
                              ->where('id', '!=', $request->codeid)
                              ->first();

            if ($existing) {
                $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This date is already marked as a holiday.</b></div>";
                return response()->json(['status'=> 303, 'message'=> $message]);
            }
        }

        $holiday->title = $request->title;
        $holiday->month = $month;
        $holiday->day = $day;
        $holiday->updated_by = auth()->id();

        if ($holiday->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Holiday Updated Successfully.</b></div>";
            return response()->json(['status'=> 300, 'message'=> $message]);
        } else {
            return response()->json(['status'=> 303, 'message'=> 'Server Error!!']);
        }
    }

    public function holidayDelete($id)
    {
        $holiday = Holiday::find($id);
        
        if (!$holiday) {
            return response()->json(['success' => false, 'message' => 'Holiday not found.'], 404);
        }

        if ($holiday->delete()) {
            return response()->json(['success' => true, 'message' => 'Holiday deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete holiday.'], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $holiday = Holiday::find($request->holiday_id);
        if (!$holiday) {
            return response()->json(['status' => 404, 'message' => 'Holiday not found']);
        }

        $holiday->status = $request->status;
        $holiday->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}