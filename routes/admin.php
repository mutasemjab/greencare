<?php

use App\Http\Controllers\Admin\AppConfigController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\AppointmentProviderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\FamilyController;
use App\Http\Controllers\Admin\MedicationController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\NurseController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PledgeFormController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProviderCategoryController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ReportTemplateController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\SpecialMedicalFormController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\TypeElderlyCareController;
use App\Http\Controllers\Admin\TypeHomeXrayController;
use App\Http\Controllers\Admin\TypeMedicalTestController;
use App\Http\Controllers\Admin\TypeRequestNurseController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Permission\Models\Permission;
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

define('PAGINATION_COUNT', 11);
Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {



    Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');

        /*         start  update login admin                 */
        Route::get('/admin/edit/{id}', [LoginController::class, 'editlogin'])->name('admin.login.edit');
        Route::post('/admin/update/{id}', [LoginController::class, 'updatelogin'])->name('admin.login.update');
        /*         end  update login admin                */

        /// Role and permission
        Route::resource('employee', 'App\Http\Controllers\Admin\EmployeeController', ['as' => 'admin']);
        Route::get('role', 'App\Http\Controllers\Admin\RoleController@index')->name('admin.role.index');
        Route::get('role/create', 'App\Http\Controllers\Admin\RoleController@create')->name('admin.role.create');
        Route::get('role/{id}/edit', 'App\Http\Controllers\Admin\RoleController@edit')->name('admin.role.edit');
        Route::patch('role/{id}', 'App\Http\Controllers\Admin\RoleController@update')->name('admin.role.update');
        Route::post('role', 'App\Http\Controllers\Admin\RoleController@store')->name('admin.role.store');
        Route::post('admin/role/delete', 'App\Http\Controllers\Admin\RoleController@delete')->name('admin.role.delete');

        Route::get('/permissions/{guard_name}', function ($guard_name) {
            return response()->json(Permission::where('guard_name', $guard_name)->get());
        });


        // Notification
        Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');


        // User (Patient) management routes
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Doctor management routes
        Route::resource('doctors', DoctorController::class);
        Route::post('doctors/{doctor}/toggle-status', [DoctorController::class, 'toggleStatus'])->name('doctors.toggle-status');

        // Nurse management routes
        Route::resource('nurses', NurseController::class);
        Route::post('nurses/{nurse}/toggle-status', [NurseController::class, 'toggleStatus'])->name('nurses.toggle-status');
        Route::resource('banners', BannerController::class);

        Route::resource('pages', PageController::class);
        Route::resource('families', FamilyController::class);
        Route::resource('settings', SettingController::class);

        // Additional family member management routes
        Route::post('families/{family}/add-member', [FamilyController::class, 'addMember'])->name('families.add-member');
        Route::post('families/{family}/remove-member', [FamilyController::class, 'removeMember'])->name('families.remove-member');



        Route::resource('report-templates', ReportTemplateController::class);
        Route::post('report-templates/{reportTemplate}/duplicate', [ReportTemplateController::class, 'duplicate'])->name('report-templates.duplicate');

        Route::resource('rooms', RoomController::class);
        Route::post('rooms/{room}/add-user', [RoomController::class, 'addUser'])->name('rooms.add-user');
        Route::post('rooms/{room}/remove-user', [RoomController::class, 'removeUser'])->name('rooms.remove-user');
        Route::get('api/rooms', [RoomController::class, 'getRooms'])->name('api.rooms');
        Route::get('rooms/{room}/stats', [RoomController::class, 'getStats'])->name('rooms.stats');

        // Medication management routes
        Route::resource('medications', MedicationController::class);
        Route::post('medications/{medication}/toggle-active', [MedicationController::class, 'toggleActive'])->name('medications.toggle-active');
        Route::post('medication-logs/{log}/mark-taken', [MedicationController::class, 'markTaken'])->name('medication-logs.mark-taken');
        Route::post('medication-logs/{log}/mark-missed', [MedicationController::class, 'markMissed'])->name('medication-logs.mark-missed');
        Route::get('medications/{medication}/calendar', [MedicationController::class, 'getCalendarData'])->name('medications.calendar');
        Route::get('/medications/upcoming', [MedicationController::class, 'getUpcoming'])->name('api.medications.upcoming');
        Route::get('/medications/overdue', [MedicationController::class, 'getOverdue'])->name('api.medications.overdue');


        Route::resource('app-configs', AppConfigController::class);
        Route::resource('deliveries', DeliveryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('coupons', CouponController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('orders', OrderController::class);

        Route::delete('/products/images/{imageId}', [ProductController::class, 'deleteImage'])->name('products.deleteImage');

        Route::get('/usedCoupons', [CouponController::class, 'displayCouponUsed'])->name('usedCoupons.index');

        // Types Routes
        Route::resource('types', TypeController::class);

        // Provider Categories Routes
        Route::resource('provider-categories', ProviderCategoryController::class);

        // Providers Routes
        Route::resource('providers', ProviderController::class);
        Route::get('special-medical-forms', [SpecialMedicalFormController::class, 'index'])->name('special-medical-forms.index');
        Route::get('special-medical-forms/{specialMedicalForm}', [SpecialMedicalFormController::class, 'show'])->name('special-medical-forms.show');
        Route::resource('careers', CareerController::class);
        Route::get('careers/{career}/applications', [CareerController::class, 'applications'])->name('careers.applications');
        Route::post('career-applications/{application}/status', [CareerController::class, 'updateApplicationStatus'])->name('career-applications.update-status');
        Route::resource('news', NewsController::class);
        Route::get('/pledge-forms', [PledgeFormController::class, 'index'])->name('pledge-forms.index');
        Route::get('/pledge-forms/{pledgeForm}', [PledgeFormController::class, 'show'])->name('pledge-forms.show');
        Route::resource('elderly-cares', TypeElderlyCareController::class);
        Route::resource('request-nurses', TypeRequestNurseController::class);
        // Home X-ray Types Routes
        Route::resource('home-xrays', TypeHomeXrayController::class);

        // Medical Test Types Routes
        Route::resource('medical-tests', TypeMedicalTestController::class);

        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');

        Route::resource('appointment-providers', AppointmentProviderController::class);

        // Additional routes for appointment providers
        Route::post('/appointment-providers/{id}/update-status', [AppointmentProviderController::class, 'updateStatus'])
            ->name('appointment-providers.update-status');

        // ajax
        Route::get('/patients/search', [UserController::class, 'searchPatients'])->name('api.patients.search');
        Route::get('/nurse/search', [UserController::class, 'searchNurses'])->name('api.nurses.search');
        Route::get('/doctor/search', [UserController::class, 'searchDoctors'])->name('api.doctors.search');
        Route::get('/families/search', [UserController::class, 'searchFamilies'])->name('api.families.search');
        Route::get('/reportTemplates/search', [ReportTemplateController::class, 'searchTemplates'])->name('api.report-templates.search');
    });
});



Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'guest:admin'], function () {
    Route::get('login', [LoginController::class, 'show_login_view'])->name('admin.showlogin');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
});
