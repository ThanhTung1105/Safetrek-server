<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendDuressAlertJob;
use App\Jobs\SendPanicAlertJob;
use App\Models\LocationHistory;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TripController extends Controller
{
    /**
     * Start a new trip with timer
     */
    public function startTrip(Request $request)
    {
        $request->validate([
            'destination_name' => 'nullable|string|max:255',
            'duration_minutes' => 'required|integer|min:1|max:1440', // Max 24 hours
        ]);

        $user = $request->user();

        // Check if user already has an active trip
        $activeTrip = Trip::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($activeTrip) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active trip. Please end it first.',
                'data' => $activeTrip,
            ], 400);
        }

        // Calculate expected end time
        $startTime = now();
        $expectedEndTime = now()->addMinutes($request->duration_minutes);

        $trip = Trip::create([
            'user_id' => $user->id,
            'destination_name' => $request->destination_name,
            'start_time' => $startTime,
            'expected_end_time' => $expectedEndTime,
            'status' => 'active',
            'trip_type' => 'timer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trip started successfully',
            'data' => [
                'trip' => $trip,
                'time_remaining_minutes' => $request->duration_minutes,
            ],
        ], 201);
    }

    /**
     * PANIC BUTTON - Immediate emergency alert
     * No trip_id needed, creates a new panic trip instantly
     * Location is OPTIONAL - supports panic from home screen (no location) or trip screen (with location)
     */
    public function panicButton(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'battery_level' => 'nullable|integer|between:0,100',
        ]);

        $user = $request->user();

        // End any active trips before creating panic trip
        try {
            Trip::where('user_id', $user->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'completed', // Mark as completed (cancelled not in ENUM)
                    'actual_end_time' => now(),
                ]);
        } catch (\Exception $e) {
            // Log but don't fail - panic is more important
            \Log::warning('Failed to auto-cancel active trips during panic', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        // Create a panic trip immediately
        $trip = Trip::create([
            'user_id' => $user->id,
            'destination_name' => '🚨 Cảnh báo khẩn cấp (Từ trang chủ)',
            'start_time' => now(),
            'expected_end_time' => now()->addHour(), // 1 hour window
            'status' => 'panic',
            'trip_type' => 'panic',
        ]);

        // Save current location (if provided)
        if ($request->filled('latitude') && $request->filled('longitude')) {
            LocationHistory::create([
                'trip_id' => $trip->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'battery_level' => $request->battery_level,
                'timestamp' => now(),
            ]);
        }

        // Dispatch panic alert job IMMEDIATELY
        dispatch(new SendPanicAlertJob($trip, $user));

        return response()->json([
            'success' => true,
            'message' => 'Panic alert sent to your guardians',
            'data' => [
                'trip_id' => $trip->id,
                'alert_sent_at' => now()->toISOString(),
            ],
        ], 201);
    }

    /**
     * Update location during trip (Background tracking)
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'battery_level' => 'nullable|integer|between:0,100',
        ]);

        $trip = Trip::findOrFail($request->trip_id);

        // Verify this trip belongs to the authenticated user
        if ($trip->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        LocationHistory::create([
            'trip_id' => $trip->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'battery_level' => $request->battery_level,
            'timestamp' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated',
        ]);
    }

    /**
     * End trip with PIN (CRITICAL: Duress PIN Logic)
     * 
     * This method handles both safety PIN and duress PIN.
     * SECURITY REQUIREMENT: The response MUST BE IDENTICAL for both PINs
     * to prevent attackers from detecting which PIN was entered.
     */
    public function endTrip(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'pin_code' => 'required|string|size:4',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'battery_level' => 'nullable|integer|between:0,100',
        ]);

        $user = $request->user();
        $trip = Trip::findOrFail($request->trip_id);

        // Verify trip belongs to user
        if ($trip->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Check if PINs are setup
        if (!$user->is_pin_setup) {
            return response()->json([
                'success' => false,
                'message' => 'Please setup your safety PINs first',
            ], 400);
        }

        $actualEndTime = now();
        $isDuressPin = false;

        // Check which PIN was entered
        if (Hash::check($request->pin_code, $user->safety_pin_hash)) {
            // SAFETY PIN: Normal completion
            $trip->update([
                'status' => 'completed',
                'actual_end_time' => $actualEndTime,
            ]);
        } elseif (Hash::check($request->pin_code, $user->duress_pin_hash)) {
            // DURESS PIN: User is under threat
            $isDuressPin = true;
            
            // Save current location if provided by app
            if ($request->filled('latitude') && $request->filled('longitude')) {
                LocationHistory::create([
                    'trip_id' => $trip->id,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'battery_level' => $request->battery_level,
                    'timestamp' => now(),
                ]);
            }
            
            // Update trip status to duress_ended (SILENTLY)
            $trip->update([
                'status' => 'duress_ended',
                'actual_end_time' => $actualEndTime,
            ]);
        } else {
            // Invalid PIN
            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN code',
            ], 401);
        }

        // CRITICAL: Return success response BEFORE dispatching job
        // This ensures the attacker sees "success" immediately
        $response = response()->json([
            'success' => true,
            'message' => 'Trip ended successfully. You are safe!',
            'data' => [
                'trip_id' => $trip->id,
                'ended_at' => $actualEndTime->toISOString(),
            ],
        ]);

        // AFTER preparing the response, dispatch duress alert if needed
        if ($isDuressPin) {
            // Dispatch job AFTER response is prepared
            // The job will send alerts to guardians silently
            dispatch(new SendDuressAlertJob($trip, $user));
        }

        return $response;
    }

    /**
     * Get active trip for user
     */
    public function getActiveTrip(Request $request)
    {
        $trip = Trip::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$trip) {
            return response()->json([
                'success' => true,
                'message' => 'No active trip',
                'data' => null,
            ]);
        }

        // Include location history
        $trip->load('locationHistory');

        return response()->json([
            'success' => true,
            'data' => $trip,
        ]);
    }

    /**
     * Get trip history
     */
    public function getTripHistory(Request $request)
    {
        $trips = Trip::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $trips,
        ]);
    }

    /**
     * Cancel active trip
     */
    public function cancelTrip(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
        ]);

        $trip = Trip::findOrFail($request->trip_id);

        if ($trip->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $trip->update([
            'status' => 'completed',
            'actual_end_time' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trip cancelled successfully',
        ]);
    }
}
