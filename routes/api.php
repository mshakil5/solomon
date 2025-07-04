<?php
if (App::environment('production')) {
  URL::forceScheme('https');
}


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkController;
use App\Http\Controllers\Api\CallBackController;
use App\Http\Controllers\Api\FrontendController;
use App\Http\Controllers\Api\PassportAuthController;
use App\Http\Controllers\Api\PaypalController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Api\WorkTimeController;
use App\Http\Controllers\Api\ServiceController;

Route::post('/register/send-otp', [PassportAuthController::class, 'requestRegistrationToken']);
Route::post('/register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::get('check-available-user/{id}', [PassportAuthController::class, 'checkUser']);
Route::get('company-short-video', [FrontendController::class, 'companyShortVideo']);

Route::get('/about-us', [FrontendController::class, 'aboutUs']);
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy']);
Route::get('/company-details', [FrontendController::class, 'companyDetails']);
Route::get('/get-category', [FrontendController::class, 'getCategory']);
Route::post('/review', [FrontendController::class, 'reviewStore']);
Route::post('/join-us', [FrontendController::class, 'joinUsStore']);
Route::post('/request-quote', [FrontendController::class, 'requestQuoteStore']);
Route::post('/contact-us', [FrontendController::class, 'contactUs']);
Route::get('/get-in-touch', [FrontendController::class, 'getInTouch']);
Route::post('/check-post-code', [FrontendController::class, 'checkPostCode']);

Route::post('request-password-reset', [PassportAuthController::class, 'requestPasswordReset']);
Route::post('verify-reset-otp', [PassportAuthController::class, 'verifyResetOtp']);
Route::post('reset-password', [PassportAuthController::class, 'resetPassword']);

Route::group(['middleware' => ['auth:api']], function () {
  Route::get('company-status', [FrontendController::class, 'companyStatus']);
  Route::get('holidays', [FrontendController::class, 'holidays']);
  Route::get('welcome', [FrontendController::class, 'welcome']);
  Route::get('slider', [FrontendController::class, 'slider']);

  Route::put('password-change', [PassportAuthController::class, 'changePassword']);
  Route::post('logout', [PassportAuthController::class, 'logout']);
  Route::get('user-details', [UserController::class, 'index']);
  Route::post('user-profile-update', [UserController::class, 'userProfileUpdate']);
  Route::post('primary-shipping-address-update', [UserController::class, 'primaryShippingAddressUpdate']);
  Route::post('primary-billing-address-update', [UserController::class, 'primaryBillingAddressUpdate']);
  Route::get('additional-addresses', [UserController::class, 'address']);
  Route::get('default-addresses', [UserController::class, 'defaultAddresses']);
  Route::post('additional-addresses', [UserController::class, 'store']);
  Route::put('additional-addresses/{id}', [UserController::class, 'update']);
  Route::delete('additional-addresses/{id}', [UserController::class, 'destroy']);
  Route::get('works', [WorkController::class, 'userWorks']);
  Route::get('works/{id}', [WorkController::class, 'workDetails']);
  Route::post('work-store/{catId?}', [FrontendController::class, 'workStore']);
  Route::post('work/{id}', [FrontendController::class, 'workUpdate']);
  Route::delete('work/{id}', [FrontendController::class, 'deleteWork']);
  Route::get('work/invoice/{id}', [WorkController::class, 'showInvoiceApi']);
  Route::get('work/transactions/{id}', [WorkController::class, 'showTransactionsApi']);
  Route::post('call-back', [CallBackController::class, 'callBack']);
  Route::get('completed-work/{id}', [WorkController::class, 'completedWorkDetails']);
  Route::get('all-transaction', [FrontendController::class, 'getAllTransaction']);
  Route::post('paypal-payment', [PaypalController::class, 'payment']);
  Route::post('account-delete-request', [CallBackController::class, 'accountDeleteRequest']);

  Route::post('work-store', [WorkController::class, 'storeWork']);

  Route::get('/work/{id}/review', [WorkController::class, 'showReviewForm']);
  Route::post('/work-review/store', [WorkController::class, 'storeReview']);
  Route::post('/work/review/{reviewId}/reply', [WorkController::class, 'storeReply']);

  Route::get('/services', [ServiceController::class, 'getServices']);

  Route::get('/types', [ServiceController::class, 'getTypes']);

  Route::post('/booking/calculate-fee', [ServiceController::class, 'calculateFee']);
  Route::post('/new-service', [ServiceController::class, 'newServiceStore']);

  Route::post('/service-booking', [ServiceController::class, 'serviceBookingStore']);

  Route::get('/service-bookings', [ServiceController::class, 'serviceBookingIndex']);

  Route::get('/service-booking-details/{id}', [ServiceController::class, 'serviceBookingDetails']);

  Route::post('/service-booking-update/{id}', [ServiceController::class, 'serviceBookingUpdate']);

  Route::delete('/service-booking/{id}', [ServiceController::class, 'serviceBookingDelete']);

  Route::post('/service-booking-review/{id}', [ServiceController::class, 'reviewStore']);

  Route::get('/service-booking/invoices/{id}', [ServiceController::class, 'getInvoices']);

  Route::post('/cancel-booking/{id}', [ServiceController::class, 'cancelBooking']);

});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'staff'], function () {
  Route::get('/edit-profile', [StaffController::class, 'editProfile']);
  Route::post('/update-profile', [StaffController::class, 'updateProfile']);

  Route::get('tasks/{id}', [WorkController::class, 'workDetailsByStaff']);

  Route::get('/due-tasks', [WorkController::class, 'getAssignedTasks']);
  Route::get('/completed-tasks', [WorkController::class, 'getCompletedTasks']);

  Route::post('/start/task/{work_id}', [WorkTimeController::class, 'startWork']);

  Route::post('/stop/task/{work_time_id}', [WorkTimeController::class, 'stopWork']);

  Route::post('/change-work-status/{work_id}', [WorkController::class, 'changeWorkStatusStaff']);

  Route::post('/start/breaktime', [WorkTimeController::class, 'startBreak']);
  Route::post('/stop/breaktime', [WorkTimeController::class, 'stopBreak']);

  Route::get('/break-time', [WorkTimeController::class, 'breakTime']);
});
