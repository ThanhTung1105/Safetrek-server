<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GuardianController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\TripController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Mobile Flutter App)
|--------------------------------------------------------------------------
|
| These routes return JSON responses for the Flutter mobile application.
| All routes use Laravel Sanctum for API authentication.
|
*/

// Public routes (No authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Require Sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication & Profile
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/setup-pins', [AuthController::class, 'setupPins']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);
    
    // Trip Management
    Route::prefix('trips')->group(function () {
        Route::post('/start', [TripController::class, 'startTrip']);
        Route::post('/update-location', [TripController::class, 'updateLocation']);
        Route::post('/end', [TripController::class, 'endTrip']); // 🔐 DURESS PIN LOGIC
        Route::post('/cancel', [TripController::class, 'cancelTrip']);
        Route::get('/active', [TripController::class, 'getActiveTrip']);
        Route::get('/history', [TripController::class, 'getTripHistory']);
    });
    
    // Guardian Management (Emergency Contacts)
    Route::prefix('guardians')->group(function () {
        Route::get('/', [GuardianController::class, 'index']);
        Route::post('/', [GuardianController::class, 'store']);
        Route::put('/{id}/status', [GuardianController::class, 'updateStatus']);
        Route::delete('/{id}', [GuardianController::class, 'destroy']);
    });
    
    // News & Safety Tips
    Route::get('/news', [NewsController::class, 'index']);
});
