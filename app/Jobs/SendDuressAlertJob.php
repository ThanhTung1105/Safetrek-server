<?php

namespace App\Jobs;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendDuressAlertJob implements ShouldQueue
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
     * This job is triggered when a user enters their DURESS PIN,
     * indicating they are under threat. It sends emergency alerts
     * to all accepted guardians.
     */
    public function handle(): void
    {
        // Get all accepted guardians for this user
        $guardians = $this->user->guardians()
            ->where('status', 'accepted')
            ->get();

        if ($guardians->isEmpty()) {
            Log::warning("Duress alert: User {$this->user->id} has no accepted guardians");
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

        // Prepare alert message
        $alertMessage = "🚨 EMERGENCY ALERT 🚨\n\n";
        $alertMessage .= "{$this->user->full_name} has activated their DURESS signal!\n\n";
        $alertMessage .= "This means they are in danger and may be under threat.\n\n";
        $alertMessage .= "Trip started: {$this->trip->start_time->format('Y-m-d H:i:s')}\n";
        $alertMessage .= "Destination: " . ($this->trip->destination_name ?? 'Not specified') . "\n\n";
        $alertMessage .= "📍 Last known location:\n{$mapsLink}\n\n";
        $alertMessage .= "🔋 Battery level: {$batteryLevel}%\n\n";
        $alertMessage .= "⚠️ IMMEDIATE ACTION REQUIRED:\n";
        $alertMessage .= "1. Try to contact {$this->user->full_name} immediately\n";
        $alertMessage .= "2. If no response, contact local authorities (113)\n";
        $alertMessage .= "3. Provide them with the location link above\n\n";
        $alertMessage .= "SafeTrek - Personal Safety App";

        // Send alerts to all guardians
        foreach ($guardians as $guardian) {
            try {
                // TODO: Implement actual SMS/Push notification sending
                // For now, we'll log it
                Log::emergency("DURESS ALERT sent to guardian: {$guardian->contact_name} ({$guardian->contact_phone_number})", [
                    'user_id' => $this->user->id,
                    'trip_id' => $this->trip->id,
                    'message' => $alertMessage,
                ]);

                // TODO: Send SMS
                // $this->sendSms($guardian->contact_phone_number, $alertMessage);

                // TODO: Send Push Notification if guardian is also a user
                // $this->sendPushNotification($guardian, $alertMessage);

            } catch (\Exception $e) {
                Log::error("Failed to send duress alert to guardian {$guardian->id}: " . $e->getMessage());
            }
        }

        // Log the duress event for admin review
        Log::emergency("DURESS EVENT LOGGED", [
            'user_id' => $this->user->id,
            'user_name' => $this->user->full_name,
            'trip_id' => $this->trip->id,
            'location' => $mapsLink,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * TODO: Implement SMS sending
     */
    private function sendSms($phoneNumber, $message)
    {
        // Integrate with SMS provider (Twilio, AWS SNS, etc.)
    }

    /**
     * TODO: Implement Push Notification
     */
    private function sendPushNotification($guardian, $message)
    {
        // Send FCM/APNS notification
    }
}
