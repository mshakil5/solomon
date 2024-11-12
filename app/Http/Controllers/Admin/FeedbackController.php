<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\Category;

class FeedbackController extends Controller
{
    public function getReviews()
    {
        $data =  Review::latest()->get();
        return view('admin.feedback.review', compact('data'));
    }

    public function getQuotes()
    {
        $data =  Quote::latest()->get();
        return view('admin.feedback.quote', compact('data'));
    }

    public function toggleReviewStatus(Request $request)
    {
        $review = Review::findOrFail($request->id);
        $review->status = $request->status;
        $review->save();

        return response()->json(['message' => 'Review status updated successfully!']);
    }

    public function careers()
    {
        $data = Career::latest()->get();
        $categories = Category::where('status', 1)->select('id', 'name')->get();
        return view('admin.careers.index', compact('data', 'categories'));
    }

}
