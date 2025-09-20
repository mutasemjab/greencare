<?php

namespace App\Http\Controllers\Api\v1\User;

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


    Route::get('/pages/{type}', [PageController::class, 'index']);

    Route::get('/banners', [BannerController::class, 'index']); // Done

    //---------------- Auth --------------------//
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);



    // Auth Route
    Route::group(['middleware' => ['auth:user-api']], function () {



        Route::get('/home', [HomeController::class, 'index']);
        Route::get('/news/{id}', [HomeController::class, 'newsDetails']);


        Route::post('/update_profile', [AuthController::class, 'updateProfile']);
        Route::post('/delete_account', [AuthController::class, 'deleteAccount']);
        Route::get('/user_profile', [AuthController::class, 'userProfile']);

        //Notification
        Route::get('/notifications', [AuthController::class, 'notifications']);



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

         Route::get('/productFavourites', [FavouriteController::class,'index']); 
        Route::post('/productFavourites', [FavouriteController::class,'store']);

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

        // Providers API Routes
        Route::prefix('providers')->group(function () {
            Route::get('/search', [ProviderController::class, 'search']); // GET /api/v1/providers/search?search=term
            Route::get('/{id}', [ProviderController::class, 'show']); // GET /api/v1/providers/{id}
            Route::get('/category/{categoryId}', [ProviderController::class, 'getByCategory']); // GET /api/v1/providers/category/{categoryId}
        });
        
    });
});
