<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\Lab\LabAppointmentController;
use App\Http\Controllers\Api\v1\Lab\LabAuthController;
use App\Http\Controllers\Api\v1\User\NewsController;
use App\Http\Controllers\Api\v1\User\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\User\AuthController;
use App\Http\Controllers\Api\v1\User\UserAddressController;
use App\Http\Controllers\Api\v1\User\PageController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Route unAuth
Route::group(['prefix' => 'v1/user'], function () {

    Route::get('appointment-service-types', [AppointmentApiController::class, 'getServiceTypes']);
    Route::get('/pages/{type}', [PageController::class, 'index']);

    Route::get('/banners', [BannerController::class, 'index']); // Done
    Route::get('/bannersForShop', [BannerController::class, 'getBannersForShop']); // Done

    //---------------- Auth --------------------//
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/settings', [SettingController::class, 'index']);
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{id}', [NewsController::class, 'newsDetails']);

    // Types API Routes
    Route::prefix('types')->group(function () {
        Route::get('/', [TypeController::class, 'index']); // GET /api/v1/types
    });



    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'getProductsFromCategory']);
    Route::get('/categories/{id}/getChildrenCategory', [CategoryController::class, 'getChildrenCategory']);

    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{id}', [BrandController::class, 'getProductsFromBrand']);

    Route::get('/products/{id}', [ProductController::class, 'productDetails']);
    Route::get('/product/search', [ProductController::class, 'searchProduct']);
    Route::post('/products', [ProductController::class, 'getProducts']);

    Route::get('/featuredProducts', [ProductController::class, 'featuredProducts']);

    // Provider Categories API Routes
    Route::prefix('provider-categories')->group(function () {
        Route::get('/type/{typeId}', [ProviderCategoryController::class, 'getByType']); // GET /api/v1/provider-categories/type/{typeId}
    });

    // Auth Route
    Route::group(['middleware' => ['auth:user-api']], function () {


        // Get all user appointments (across all rooms)
        Route::get('/appointmentsResultFromLab', [UserAppointmentResultsController::class, 'getAppointmentResults']);
        
        // image for chat
        Route::get('/uploadPhotoVoice', [UploadPhotoVoiceController::class, 'index']);
        Route::post('/uploadPhotoVoice', [UploadPhotoVoiceController::class, 'store']);

        Route::post('updateFcmToken', [AuthController::class, 'updateFcmToken']);




        Route::post('/update_profile', [AuthController::class, 'updateProfile']);
        Route::post('/delete_account', [AuthController::class, 'deleteAccount']);
        Route::get('/user_profile', [AuthController::class, 'userProfile']);

        //Notification
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('notifications/send', [NotificationController::class, 'sendNotification']);
        // Send notification to multiple users
        Route::post('notifications/send-bulk', [NotificationController::class, 'sendBulkNotification']);


        Route::get('/delivery', [UserAddressController::class, 'getDelivery']); // Done

        //-------------------- Address ------------------------//
        Route::get('/addresses', [UserAddressController::class, 'index']); // Done
        Route::post('/addresses', [UserAddressController::class, 'store']); // Done
        Route::post('/addresses/{address_id}', [UserAddressController::class, 'update']); // Done
        Route::delete('/addresses/{id}', [UserAddressController::class, 'destroy']); // Done



        Route::get('/productFavourites', [FavouriteController::class, 'index']);
        Route::post('/productFavourites', [FavouriteController::class, 'store']);

        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::delete('/cart/{id}', [CartController::class, 'delete']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{id}', [OrderController::class, 'details']);
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);

        Route::get('/coupons', [CouponController::class, 'index']);
        Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);




        Route::post('/appointment-providers', [AppointmentProviderController::class, 'store']);
        // Get user appointments
        Route::get('/appointment-providers/user', [AppointmentProviderController::class, 'getUserAppointments']);



        // api for rooms
        Route::post('rooms', [RoomReportController::class, 'createRoom']);
        Route::get('reportTemplates', [RoomReportController::class, 'getReportTemplates']);

        // Get specific template with details
        Route::get('/reportTemplates/{template_id}', [RoomReportController::class, 'getTemplateDetails']);

        // Submit initial report
        Route::post('reports/initial', [RoomReportController::class, 'submitInitialReport']);

        // Get available templates and pending reports for recurring reports
        Route::get('rooms/{room_id}/templates', [RoomReportController::class, 'getAvailableTemplates']);

        // Submit recurring reports
        Route::post('reports/recurring', [RoomReportController::class, 'submitRecurringReport']);
        Route::post('/reports/by-time', [RoomReportController::class, 'getReportsByTime']);

        // Get reports history
        Route::get('rooms/{room_id}/reports', [RoomReportController::class, 'getRoomReports']);
        Route::get('reports/{report}', [RoomReportController::class, 'getReport']);

        // Get room medications
        Route::get('rooms/{room_id}/medications', [RoomReportController::class, 'getRoomMedications']);


        Route::get('/getPatient', [RoomReportController::class, 'getPatient']);
        Route::get('/rooms/createdByNurse', [RoomReportController::class, 'getNurseRooms']);

        Route::prefix('pledgeForms')->group(function () {
            Route::get('/', [PledgeFormController::class, 'index']);
            Route::post('/', [PledgeFormController::class, 'store']);
        });

        // for reminder
        Route::post('medication-logs/{log}/mark-taken', [MedicationLogController::class, 'markAsTaken']);
        Route::get('medication-logs/today', [MedicationLogController::class, 'getTodaySchedule']);
        Route::get('medication-logs/upcoming', [MedicationLogController::class, 'getUpcomingReminders']);

        Route::post('appointments', [AppointmentApiController::class, 'storeAppointment']);
        Route::get('appointments', [AppointmentApiController::class, 'getAppointmentsByType']);

        Route::post('/showers', [ShowerController::class, 'store']);

        Route::get('medications', [MedicationController::class, 'index']);
        Route::post('medications', [MedicationController::class, 'store']);
        Route::put('medications/{id}', [MedicationController::class, 'update']);
        Route::delete('medications/{id}', [MedicationController::class, 'destroy']);

        Route::post('/transfer-patients', [TransferPatientController::class, 'store']);
        Route::get('/transfer-patients', [TransferPatientController::class, 'index']);
        // Get all active careers
        Route::get('careers', [CareerApplicationApiController::class, 'getCareers']);

        // Submit career application
        Route::post('careers/apply', [CareerApplicationApiController::class, 'submitApplication']);

        // Get my applications
        Route::get('careers/my-applications', [CareerApplicationApiController::class, 'myApplications']);

        // Get all forms in a room
        Route::get('rooms/{roomId}/special-medical-forms', [SpecialMedicalFormApiController::class, 'getRoomForms']);

        // Create a new form
        Route::post('special-medical-forms', [SpecialMedicalFormApiController::class, 'createForm']);

        // Get form details with replies
        Route::get('special-medical-forms/{formId}', [SpecialMedicalFormApiController::class, 'getFormDetails']);

        // Reply to a form
        Route::post('special-medical-forms/{formId}/reply', [SpecialMedicalFormApiController::class, 'replyToForm']);

        // Update form status (open/closed)
        Route::post('special-medical-forms/{formId}/status', [SpecialMedicalFormApiController::class, 'updateFormStatus']);

        // Delete form
        Route::delete('special-medical-forms/{formId}', [SpecialMedicalFormApiController::class, 'deleteForm']);
    });
});

