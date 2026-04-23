<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\OtpController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\TrainerController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\PlanCategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ExpenseController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/plans/category/{slug}', [HomeController::class, 'getPlansByCategory'])->name('plans.category');

// Admin Routes
Route::prefix('rbadmin')->name('admin.')->group(function () {
    
    // Guest Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.show');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

    // Authenticated but OTP pending
    Route::middleware('auth')->group(function () {
        Route::get('/otp', [OtpController::class, 'showOtpForm'])->name('otp.show');
        Route::post('/otp', [OtpController::class, 'verify'])->name('otp.verify');
        Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    // Fully authenticated and OTP verified
    Route::middleware(['auth', 'otp.verified'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('members', MemberController::class);
        Route::post('members/import-csv', [MemberController::class, 'importCsv'])->name('members.import-csv');
        Route::post('members/import-local', [MemberController::class, 'importLocalCsv'])->name('members.import-local');
        Route::patch('plan_categories/{plan_category}/toggle', [PlanCategoryController::class, 'toggleStatus'])->name('plan_categories.toggle');
        Route::resource('plan_categories', PlanCategoryController::class);
        Route::resource('plans', PlanController::class);
        Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);
        Route::resource('trainers', TrainerController::class);
        Route::resource('facilities', FacilityController::class);
        Route::resource('expenses', ExpenseController::class);
        Route::resource('settings', SettingController::class);
    });
});
