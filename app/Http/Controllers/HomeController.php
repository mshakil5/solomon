<?php
  
namespace App\Http\Controllers;

use App\Models\ServiceBooking;
use App\Models\Work;
use App\Models\WorkTime;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkAssign;
use App\Models\WorkReview;
use Illuminate\Support\Facades\DB;
  
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function userDashboard(): View
    {
        return view('user.dashboard');
    } 
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome(): View
    {
        $newJobs = ServiceBooking::where('notified', false)->latest()->get();
        $newJobsCount = $newJobs->count();

        $placedJobsCount = ServiceBooking::where('status', 1)->count();
        $processingJobsCount = ServiceBooking::where('status', 2)->count();
        $completedJobsCount = ServiceBooking::where('status', 3)->count();
        $cancelledJobsCount = ServiceBooking::where('status', 4)->count();

        return view('admin.dashboard', compact(
            'newJobs', 'newJobsCount',
            'placedJobsCount', 'processingJobsCount',
            'completedJobsCount', 'cancelledJobsCount'
        ));
    }
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function staffHome(): View
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();
        $formattedToday = Carbon::createFromFormat('Y-m-d', $today)->format('d-m-Y');

        $assignedTasks = WorkAssign::where('staff_id', $userId)
                            ->whereHas('serviceBooking', function($query) {
                                $query->where('status', 2);
                            })
                            ->whereDate('updated_at', $today)
                            ->orderBy('id', 'DESC')
                            ->get();
                            
        $completedTasks = WorkAssign::where('staff_id', $userId)
                            ->whereHas('serviceBooking', function($query) {
                                $query->where('status', 3);
                            })
                            ->whereDate('updated_at', $today)
                            ->orderBy('id', 'DESC')
                            ->get();

        $workDurationSum = WorkTime::where('staff_id', $userId)
                                ->where('start_date', $formattedToday)
                                ->where('is_break', 0)
                                ->sum('duration');   
                                                              
        return view('staff.dashboard', compact('assignedTasks', 'completedTasks', 'workDurationSum'));
    }

    public function index()
    {
        if (auth()->user()->is_type == '1') {
            return redirect()->route('admin.dashboard');
        }
        else if (auth()->user()->is_type == '0') {
            return redirect()->route('user.profile');
        }
        else if (auth()->user()->is_type == '2') {
            return redirect()->route('staff.profile');
        }
        else{
            return view('home');
        }
    } 
    
    public function cleanDB()
    {
        $tables = [
            // 'acc_del_requests',
            'additional_addresses',
            'call_backs',
            'careers',
            'invoices',
            'jobs',
            'new_services',
            'payments',
            'quotes',
            'reviews',
            'review_answers',
            'review_questions',
            'service_bookings',
            'service_booking_reviews',
            'service_images',
            'transactions',
            'uploads',
            'works',
            'work_assigns',
            'work_images',
            'work_reviews',
            'work_review_replies',
            'work_times',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return "Cleaned successfully.";
    }
}