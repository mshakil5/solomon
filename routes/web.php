<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\WorkTimeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\CompanyDetailsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// cache clear
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});
//  cache clear

Route::post('/company-info-update', [CompanyDetailsController::class, 'updateCompanyInfo'])->name('admin.companyinfo');

Auth::routes();
Route::get('/', [FrontendController::class, 'index'])->name('homepage');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('aboutUs');
Route::post('/work', [FrontendController::class, 'workStore'])->name('work.store');
// Route::get('/contact', [FrontendController::class, 'showContactForm'])->name('contact.show');
Route::post('/contact-message', [FrontendController::class, 'contactMessage'])->name('contactMessage');

Route::get('/category/{category}/{subcategory?}', [FrontendController::class, 'showCategoryDetails'])->name('category.show');


Route::get('/privacy', [FrontendController::class, 'privacy'])->name('privacy');
Route::get('/terms', [FrontendController::class, 'terms'])->name('terms');

Route::get('/review', [FrontendController::class, 'review'])->name('review');
Route::post('/review', [FrontendController::class, 'reviewStore'])->name('review.store');

Route::get('/request-quote', [FrontendController::class, 'showRequestQuoteForm'])->name('quote.form');
Route::post('/request-quote', [FrontendController::class, 'requestQuote'])->name('quote.request');

Route::get('/check-city', [FrontendController::class, 'checkCity'])->name('check.city');
Route::get('/suggest-city', [FrontendController::class, 'suggestCity'])->name('suggest.city');

Route::get('password/request', [LoginController::class, 'showPasswordRequestForm'])->name('password.request.form');
Route::post('password/request', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');

Route::post('/check-post-code', [FrontendController::class, 'checkPostCode']);

Route::get('/join-us', [FrontendController::class, 'joinUs'])->name('join.us');
Route::post('/join-us', [FrontendController::class, 'joinUsStore'])->name('join.us.store');

Route::post('/callback-request', [FrontendController::class, 'callBack'])->name('callRequest');

// payment
Route::post('pay/{id}', [PaypalController::class, 'pay'])->name('payment');
Route::get('success', [PaypalController::class, 'success']);
Route::get('error', [PaypalController::class, 'error']);


/*------------------------------------------
--------------------------------------------
All Normal Users Routes List
--------------------------------------------
--------------------------------------------*/
Route::group(['prefix' => 'user/', 'middleware' => ['auth', 'is_user']], function () {

    Route::get('/home', [HomeController::class, 'userDashboard'])->name('user.home');
    Route::get('/works', [WorkController::class, 'userWorks'])->name('user.works');

    Route::get('/edit-work/{id}', [WorkController::class, 'editWork'])->name('work.edit');

    Route::get('/work/{id}', [WorkController::class, 'showDetails'])->name('show.details');

    Route::put('/work-update', [WorkController::class, 'workUpdate'])->name('work.update');
    Route::get('/work-images/{id}', [WorkController::class, 'workDetailsByUser'])->name('user.work.images');

    Route::delete('/work/{id}', [WorkController::class, 'destroy'])->name('work.destroy');

    Route::get('/work/{work}/invoice', [InvoiceController::class, 'showInvoice'])->name('show.invoice');

    Route::get('/work/{work}/transactions', [WorkController::class, 'showTransactions'])->name('show.transactions');

    Route::get('/work/{id}/review', [WorkController::class, 'showReviewForm'])->name('work.review');

    Route::post('/work-review/store', [WorkController::class, 'storeReview'])->name('work.review.store');

    Route::post('/work/review/{reviewId}/reply', [WorkController::class, 'storeReply'])->name('work.review.reply.store');

    Route::get('/profile', [UserController::class, 'userProfile'])->name('user.profile');
    Route::post('/user/profile/update', [UserController::class, 'userProfileUpdate'])->name('user.profile.update');
    Route::get('/password', [UserController::class, 'password'])->name('user.password');
    Route::post('/password', [UserController::class, 'updatePassword'])->name('user.update.password');


    Route::get('/additional-addresses', [UserController::class, 'index'])->name('additional-addresses.index');
    Route::get('/additional-addresses/create', [UserController::class, 'create'])->name('additional-addresses.create');
    Route::post('/additional-addresses', [UserController::class, 'store'])->name('additional-addresses.store');
    Route::get('/additional-addresses/{id}/edit', [UserController::class, 'edit'])->name('additional-addresses.edit');
    Route::put('/additional-addresses/{id}', [UserController::class, 'update'])->name('additional-addresses.update');
    Route::delete('/additional-addresses/{id}', [UserController::class, 'destroy'])->name('additional-addresses.destroy');
});


/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::group(['prefix' => 'staff/', 'middleware' => ['auth', 'is_manager']], function () {

    // Dashboard
    Route::get('/home', [HomeController::class, 'staffHome'])->name('staff.home');

    // Edit profile
    Route::get('/edit-profile', [StaffController::class, 'editProfile'])->name('staff.profile.edit');
    Route::post('/update-profile', [StaffController::class, 'updateProfile']);

    // Due tasks
    Route::get('/due-tasks', [WorkController::class, 'getAssignedTasks'])->name('assigned.tasks.staff');

    // Completed tasks
    Route::get('/completed-tasks', [WorkController::class, 'getCompletedTasks'])->name('completed.tasks.staff');

    // Work details
    Route::get('/work/{id}', [WorkController::class, 'workDetailsByStaff'])->name('staff.work.details');
    Route::get('/work-gallery/{id}', [WorkController::class, 'workDetailsUploadByStaff'])->name('staff.work.images');
    Route::post('/work-gallery-upload', [WorkController::class, 'workImageUploadByStaff'])->name('staff.workimages.upload');

    // Work start, stop , Break start, stop
    Route::post('/worktime/start', [WorkTimeController::class, 'startWork'])->name('worktime.start');
    Route::post('/worktime/stop', [WorkTimeController::class, 'stopWork'])->name('worktime.stop');
    Route::post('/breaktime/start', [WorkTimeController::class, 'startBreak'])->name('worktime.startBreak');
    Route::post('/breaktime/stop', [WorkTimeController::class, 'stopBreak'])->name('worktime.stopBreak');

    Route::get('/check-break', [WorkTimeController::class, 'checkBreak'])->name('checkBreak');

    // Change status by staff
    Route::get('/change-work-status', [WorkController::class, 'changeWorkStatusStaff']);
    // Upload image of completed tasks
    Route::get('/upload/{id}', [WorkController::class, 'uploadPage'])->name('upload.page');
    Route::post('/upload-file', [WorkController::class, 'uploadFile'])->name('upload-file');
    Route::delete('/upload/{id}', [WorkController::class, 'deleteFile'])->name('upload.delete');


    


});
