<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Admin Dashboard)
|--------------------------------------------------------------------------
|
| These routes return Blade views for the web admin dashboard.
| TODO: Add authentication middleware for admin routes.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Admin routes (TODO: Add 'auth' and 'role:admin' middleware)
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Trip Monitoring (View trip history, maps, etc.)
    Route::resource('trips', TripController::class)->only(['index', 'show']);
    
    // Content Management (News, Safety Tips)
    Route::resource('posts', PostController::class);
});
