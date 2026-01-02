<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

// Trang login phải nằm ngoài middleware 'auth' để có thể truy cập được
Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');


/*
|--------------------------------------------------------------------------
| Protected Routes (Bắt buộc đăng nhập mới được vào)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // 1. Điều hướng gốc: Nếu vào '/', bắt buộc check auth rồi mới cho sang dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // 2. Nhóm Admin Dashboard
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Trang chủ quản trị (Hiển thị Dashboard_admin.php)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Chi tiết người dùng (Hiển thị user_information.php)
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        
        // Chi tiết chuyến đi (Hiển thị trip_detail.blade.php)
        Route::get('/trips/{id}', [TripController::class, 'show'])->name('trips.show');

    });
});