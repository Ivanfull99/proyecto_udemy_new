<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Rol\RolesController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Admin\Staff\StaffsController;
use App\Http\Controllers\Admin\Doctor\DoctorsController;
use App\Http\Controllers\Dashboard\DashboardkpiController;
use App\Http\Controllers\Admin\Doctor\SpecialityController;
use App\Http\Controllers\Appointment\AppointmentController;
use App\Http\Controllers\Appointment\AppointmentPayController;
use App\Http\Controllers\Appointment\AppointmentAttentionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
 
    //'middleware' => 'api',
    'prefix' => 'auth',
    //'middleware' => ['role:admin','permission:publish articles'],
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/list', [AuthController::class, 'list']);
    Route::post('/reg', [AuthController::class, 'reg']);
 
}); 

Route::group([
    'middleware' => 'auth:api'
], function ($router) {
    Route::resource("roles",RolesController::class);

    Route::get("staffs/config",[StaffsController::class,"config"]);
    Route::post("staffs/{id}",[StaffsController::class,"update"]);
    Route::resource("staffs",StaffsController::class);
    //
    Route::resource("specialities",SpecialityController::class);
    //
    Route::get("doctors/profile/{id}",[DoctorsController::class,"profile"]);
    Route::get("doctors/config",[DoctorsController::class,"config"]);
    Route::post("doctors/{id}",[DoctorsController::class,"update"]);
    Route::resource("doctors",DoctorsController::class);
    //
    Route::get("patients/profile/{id}",[PatientController::class,"profile"]);
    Route::post("patients/{id}",[PatientController::class,"update"]);
    Route::resource("patients",PatientController::class);
    //

    Route::get("appointment/config",[AppointmentController::class,"config"]);
    Route::get("appointment/patient",[AppointmentController::class,"query_patient"]);

    Route::post("appointment/filter",[AppointmentController::class,"filter"]);
    Route::post("appointment/calendar",[AppointmentController::class,"calendar"]);

    Route::resource("appointment",AppointmentController::class);

    Route::resource("appointment-pay",AppointmentPayController::class);
    Route::resource("appointment-attention",AppointmentAttentionController::class);

    Route::post("dashboard/admin",[DashboardkpiController::class,"dashboard_admin"]);
    Route::post("dashboard/admin-year",[DashboardkpiController::class,"dashboard_admin_year"]);

    Route::post("dashboard/doctor",[DashboardkpiController::class,"dashboard_doctor"]);
    Route::get("/dashboard/config",[DashboardkpiController::class,"config"]);
    Route::post("dashboard/doctor-year",[DashboardkpiController::class,"dashboard_doctor_year"]);
    

    


    
}); 