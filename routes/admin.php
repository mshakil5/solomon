<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Admin\CompanyDetailsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\WorkTimeController;

/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){
  
    
    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');
    //profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('profile/{id}', [AdminController::class, 'adminProfileUpdate']);
    Route::post('changepassword', [AdminController::class, 'changeAdminPassword']);
    Route::put('image/{id}', [AdminController::class, 'adminImageUpload']);
    //profile end

    Route::get('/new-admin', [AdminController::class, 'getAdmin'])->name('alladmin');
    Route::post('/new-admin', [AdminController::class, 'adminStore']);
    Route::get('/new-admin/{id}/edit', [AdminController::class, 'adminEdit']);
    Route::post('/new-admin-update', [AdminController::class, 'adminUpdate']);
    Route::get('/new-admin/{id}', [AdminController::class, 'adminDelete']);

    Route::get('/get-all-work', [WorkController::class, 'index'])->name('admin.work');
    Route::get('/get-new', [WorkController::class, 'new'])->name('admin.new');
    Route::get('/get-processing', [WorkController::class, 'processing'])->name('admin.processing');
    Route::get('/get-complete', [WorkController::class, 'complete'])->name('admin.complete');
    Route::get('/get-cancel', [WorkController::class, 'cancel'])->name('admin.cancel');

    Route::post('/assign-staff', [WorkController::class, 'assignStaff'])->name('assign.staff');

    Route::get('/get-all-work/{id}', [WorkController::class, 'workGallery'])->name('admin.workGallery');
    Route::get('/work/{id}', [WorkController::class, 'workDetailsByAdmin'])->name('admin.work.details');

    Route::post('/worktime/start', [WorkTimeController::class, 'startWorkByAdmin'])->name('worktime.start.admin');
    Route::post('/worktime/stop', [WorkTimeController::class, 'stopWorkByAdmin'])->name('worktime.stop.admin');

    Route::get('/work-time/{id}', [WorkTimeController::class, 'workTimeByAdmin'])->name('admin.work.timer.details');

    Route::put('/worktime/{id}', [WorkTimeController::class, 'update'])->name('worktime.update');

    Route::post('/worktime-store', [WorkTimeController::class, 'storeWorkTimeByAdmin'])->name('worktime.store');

    Route::delete('/worktime/{id}', [WorkTimeController::class, 'destroy'])->name('worktime.destroy');


    Route::get('/work/{id}/review', [WorkController::class, 'showAdminReview'])->name('admin.work.review');

    Route::post('/review/{review}/reply', [WorkController::class, 'storeReplyByAdmin'])->name('admin.review.reply.store');

    Route::get('/all-transaction', [TransactionController::class, 'allTransactions'])->name('allTransactions');

    Route::get('/work/transaction/{id}', [TransactionController::class, 'showTransactions'])->name('work.transactions');
    Route::get('/add/transaction/{work_id}', [TransactionController::class, 'addTransaction'])->name('add.transaction');
    Route::post('/transaction', [TransactionController::class, 'store'])->name('store.transaction');
    Route::get('/transaction/{id}/edit', [TransactionController::class, 'edit'])->name('transaction.edit');
    Route::post('/transaction/update', [TransactionController::class, 'update'])->name('transaction.update');
    Route::delete('/transaction/{id}', [TransactionController::class, 'destroy'])->name('transaction.destroy');


    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit']);
    Route::post('/transactions-update', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

    Route::get('/change-work-status', [WorkController::class, 'changeWorkStatus']);
    Route::get('/view-image/{id}', [WorkController::class, 'viewImage'])->name('view.image');

    // location
    Route::get('/location', [LocationController::class, 'index'])->name('admin.location');
    Route::post('/location', [LocationController::class, 'store']);
    Route::get('/location/{id}/edit', [LocationController::class, 'edit']);
    Route::post('/location-update', [LocationController::class, 'update']);
    Route::get('/location/{id}', [LocationController::class, 'delete']);

    Route::post('/location-status', [LocationController::class, 'toggleStatus']);

    //Invoice
    Route::get('/invoice/{id}', [InvoiceController::class, 'index'])->name('work.invoice');
    Route::get('/invoices/create/{work_id}', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/admin/invoices/{work_id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/admin/invoices/{work_id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    //User crud by Admin
    Route::get('/new-user', [UserController::class, 'getUser'])->name('allUser');
    Route::post('/new-user', [UserController::class, 'userStore']);
    Route::get('/new-user/{id}/edit', [UserController::class, 'userEdit']);
    Route::post('/new-user-update', [UserController::class, 'userUpdate']);
    Route::get('/new-user/{id}', [UserController::class, 'userDelete']);

    //Staff crud by Admin
    Route::get('/staff', [StaffController::class, 'getStaff'])->name('allStaff');
    Route::post('/staff', [StaffController::class, 'staffStore']);
    Route::get('/staff/{id}/edit', [StaffController::class, 'staffEdit']);
    Route::post('/staff-update', [StaffController::class, 'staffUpdate']);
    Route::get('/staff/{id}', [StaffController::class, 'staffDelete']);

    Route::get('/user-delete-request', [UserController::class, 'getUserDeleteRequest'])->name('allUserDeleteReq');

    
    //job post by Admin
    Route::get('/job', [JobController::class, 'getjob'])->name('admin.job');
    Route::post('/job', [JobController::class, 'jobStore'])->name('job.store');

    // company information
    Route::get('/company-details', [CompanyDetailsController::class, 'index'])->name('admin.companyDetail');

    Route::get('/about-us', [CompanyDetailsController::class, 'aboutUs'])->name('admin.aboutUs');
    Route::post('/about-us', [CompanyDetailsController::class, 'aboutUsUpdate'])->name('admin.aboutUs');

    Route::get('/home-footer', [CompanyDetailsController::class, 'homeFooter'])->name('admin.homeFooter');
    Route::post('/home-footer', [CompanyDetailsController::class, 'homeFooterUpdate'])->name('admin.homeFooter');
    

    // Category crud
    Route::get('/category', [CategoryController::class, 'getCategory'])->name('allcategory');
    Route::post('/category', [CategoryController::class, 'categoryStore']);
    Route::get('/category/{id}/edit', [CategoryController::class, 'categoryEdit']);
    Route::post('/category-update', [CategoryController::class, 'categoryUpdate']);
    Route::get('/category/{id}', [CategoryController::class, 'categoryDelete']);
    Route::post('/category-status', [CategoryController::class, 'toggleStatus']);

    //Feedbacks
    Route::get('/reviews', [FeedbackController::class, 'getReviews'])->name('allReviews');
    Route::get('/quotes', [FeedbackController::class, 'getQuotes'])->name('allQuotes');

    Route::post('/toggle-review-status', [FeedbackController::class, 'toggleReviewStatus']);

    Route::get('/careers', [FeedbackController::class, 'careers'])->name('admin.careers.index');    

    //Staff crud by Admin
    Route::get('/questions', [QuestionController::class, 'index'])->name('allQuestions');
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::get('/questions/{id}/edit', [QuestionController::class, 'edit']);
    Route::post('/questions-update', [QuestionController::class, 'update']);
    Route::get('/questions/{id}', [QuestionController::class, 'delete']);

});
  