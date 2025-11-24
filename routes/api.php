<?php

namespace App\Http\Controllers\Api\v1\User;

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
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/settings', [SettingController::class, 'index']);


    // Auth Route
    Route::group(['middleware' => ['auth:user-api']], function () {


        // image for chat
        Route::get('/uploadPhotoVoice', [UploadPhotoVoiceController::class, 'index']);
        Route::post('/uploadPhotoVoice', [UploadPhotoVoiceController::class, 'store']);
        
        Route::post('updateFcmToken', [AuthController::class, 'updateFcmToken']);

        Route::get('/home', [HomeController::class, 'index']);
        Route::get('/news', [NewsController::class, 'index']);
        Route::get('/news/{id}', [NewsController::class, 'newsDetails']);


        Route::post('/update_profile', [AuthController::class, 'updateProfile']);
        Route::post('/delete_account', [AuthController::class, 'deleteAccount']);
        Route::get('/user_profile', [AuthController::class, 'userProfile']);

        //Notification
        Route::get('/notifications', [NotificationController::class, 'index']);



        Route::get('/delivery', [UserAddressController::class, 'getDelivery']); // Done

        //-------------------- Address ------------------------//
        Route::get('/addresses', [UserAddressController::class, 'index']); // Done
        Route::post('/addresses', [UserAddressController::class, 'store']); // Done
        Route::post('/addresses/{address_id}', [UserAddressController::class, 'update']); // Done
        Route::delete('/addresses/{id}', [UserAddressController::class, 'destroy']); // Done



        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{id}', [CategoryController::class, 'getProductsFromCategory']);
        Route::get('/categories/{id}/getChildrenCategory', [CategoryController::class, 'getChildrenCategory']);

        Route::get('/brands', [BrandController::class, 'index']);
        Route::get('/brands/{id}', [BrandController::class, 'getProductsFromBrand']);

        Route::get('/products/{id}', [ProductController::class, 'productDetails']);
        Route::get('/product/search', [ProductController::class, 'searchProduct']);
        Route::post('/products', [ProductController::class, 'getProducts']);

        Route::get('/featuredProducts', [ProductController::class, 'featuredProducts']);

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


        // Types API Routes
        Route::prefix('types')->group(function () {
            Route::get('/', [TypeController::class, 'index']); // GET /api/v1/types
        });

        // Provider Categories API Routes
        Route::prefix('provider-categories')->group(function () {
            Route::get('/type/{typeId}', [ProviderCategoryController::class, 'getByType']); // GET /api/v1/provider-categories/type/{typeId}
        });

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

        Route::post('appointments', [AppointmentApiController::class, 'storeAppointment']);
        Route::get('appointments', [AppointmentApiController::class, 'getAppointmentsByType']);

        Route::post('/showers', [ShowerController::class, 'store']);

        Route::get('medications', [MedicationController::class, 'index']);
        Route::post('medications', [MedicationController::class, 'store']);
        Route::put('medications/{id}', [MedicationController::class, 'update']);
        Route::delete('medications/{id}', [MedicationController::class, 'destroy']);


    });
});
