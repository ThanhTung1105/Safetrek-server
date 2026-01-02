<?php

namespace App\Jobs;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendTimerExpiredAlertJob implements ShouldQueue
{
    use Queueable;

    protected $trip;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(Trip $trip, User $user)
    {
        $this->trip = $trip;
        $this->user = $user;
    }

    /**
     * Execute the job.
     * 
     * This job is triggered when trip timer expires and user
     * has not checked in as safe. It sends warning alerts
     * to all accepted guardians.
     */
    public function handle(): void
    {
        $notificationService = app(\App\Services\NotificationService::class);

        // Get all accepted guardians for this user
        $guardians = $this->user->guardians()
            ->where('status', 'accepted')
            ->get();

        if ($guardians->isEmpty()) {
            Log::warning("Timer expired alert: User {$this->user->id} has no accepted guardians");
            return;
        }

        // Get last known location
        $lastLocation = $this->trip->locationHistory()
            ->orderBy('timestamp', 'desc')
            ->first();

        $latitude = $lastLocation ? $lastLocation->latitude : 'Unknown';
        $longitude = $lastLocation ? $lastLocation->longitude : 'Unknown';
        $batteryLevel = $lastLocation ? $lastLocation->battery_level : 'Unknown';

        // Generate Google Maps link
        $mapsLink = ($latitude !== 'Unknown' && $longitude !== 'Unknown')
            ? "https://www.google.com/maps?q={$latitude},{$longitude}"
            : 'Location unavailable';

        // Prepare alert data
        $alertData = [
            'user_name' => $this->user->full_name,
            'start_time' => $this->trip->start_time->format('Y-m-d H:i:s'),
            'expected_end_time' => $this->trip->expected_end_time->format('Y-m-d H:i:s'),
            'destination' => $this->trip->destination_name,
            'maps_link' => $mapsLink,
            'battery_level' => $batteryLevel,
        ];

        // Send timer expired alerts to all guardians
        foreach ($guardians as $guardian) {
            try {
                $notificationService->sendEmergencyAlert(
                    recipientName: $guardian->contact_name,
                    recipientPhone: $guardian->contact_phone_number,
                    recipientEmail: null,
                    alertType: 'timer_expired',
                    alertData: $alertData
                );

                Log::warning("TIMER EXPIRED ALERT sent to guardian: {$guardian->contact_name} ({$guardian->contact_phone_number})", [
                    'user_id' => $this->user->id,
                    'trip_id' => $this->trip->id,
                    'guardian_id' => $guardian->id,
                    'alert_type' => 'timer_expired',
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to send timer expired alert to guardian {$guardian->id}: " . $e->getMessage());
            }
        }

        // Update trip status to alerted
        $this->trip->update([
            'status' => 'alerted',
        ]);

        // Log the timer expired event
        Log::warning("TIMER EXPIRED EVENT", [
            'user_id' => $this->user->id,
            'user_name' => $this->user->full_name,
            'trip_id' => $this->trip->id,
            'expected_end_time' => $this->trip->expected_end_time->toISOString(),
            'location' => $mapsLink,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
