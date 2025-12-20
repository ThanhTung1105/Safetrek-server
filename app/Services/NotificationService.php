<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * NotificationService
 * 
 * Centralized service for sending notifications via multiple channels:
 * - SMS (Twilio, AWS SNS, etc.)
 * - Push Notifications (FCM)
 * - Email (SMTP)
 * 
 * For development: Logs notifications to laravel.log
 * For production: Integrate with actual providers
 */
class NotificationService
{
    /**
     * Send emergency alert via all available channels
     */
    public function sendEmergencyAlert(
        string $recipientName,
        string $recipientPhone,
        ?string $recipientEmail,
        string $alertType,
        array $alertData
    ): bool {
        $success = true;

        // Prepare message
        $message = $this->buildAlertMessage($alertType, $alertData);

        // Send via SMS (highest priority)
        try {
            $this->sendSms($recipientPhone, $message);
        } catch (\Exception $e) {
            Log::error("SMS sending failed to {$recipientPhone}: " . $e->getMessage());
            $success = false;
        }

        // Send via Email (backup channel)
        if ($recipientEmail) {
            try {
                $this->sendEmail($recipientEmail, $recipientName, $alertType, $message);
            } catch (\Exception $e) {
                Log::error("Email sending failed to {$recipientEmail}: " . $e->getMessage());
            }
        }

        // Send Push Notification if available
        try {
            $this->sendPushNotification($recipientPhone, $alertType, $alertData);
        } catch (\Exception $e) {
            Log::error("Push notification failed to {$recipientPhone}: " . $e->getMessage());
        }

        return $success;
    }

    /**
     * Send SMS message
     */
    public function sendSms(string $phoneNumber, string $message): void
    {
        // TODO: Integrate with SMS provider (Twilio, AWS SNS, etc.)
        // Example Twilio integration:
        // $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        // $twilio->messages->create($phoneNumber, [
        //     'from' => config('services.twilio.from'),
        //     'body' => $message
        // ]);

        // For development: Log the SMS
        Log::channel('single')->info("📱 SMS SENT", [
            'to' => $phoneNumber,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ]);

        // Store in database for debugging
        \DB::table('notifications_log')->insert([
            'type' => 'sms',
            'recipient' => $phoneNumber,
            'message' => $message,
            'status' => 'logged',
            'created_at' => now(),
        ]);
    }

    /**
     * Send email message
     */
    public function sendEmail(
        string $email,
        string $recipientName,
        string $alertType,
        string $message
    ): void {
        // TODO: Create mail template and send
        // Mail::to($email)->send(new EmergencyAlertMail($alertType, $message));

        // For development: Log the email
        Log::channel('single')->info("📧 EMAIL SENT", [
            'to' => $email,
            'recipient_name' => $recipientName,
            'subject' => "🚨 SafeTrek Emergency Alert - {$alertType}",
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ]);

        // Store in database for debugging
        \DB::table('notifications_log')->insert([
            'type' => 'email',
            'recipient' => $email,
            'message' => $message,
            'status' => 'logged',
            'created_at' => now(),
        ]);
    }

    /**
     * Send push notification via FCM
     */
    public function sendPushNotification(string $identifier, string $alertType, array $data): void
    {
        // TODO: Integrate with FCM
        // $fcmToken = User::where('phone_number', $identifier)->value('fcm_token');
        // if ($fcmToken) {
        //     $fcm->send($fcmToken, $data);
        // }

        // For development: Log the push notification
        Log::channel('single')->info("🔔 PUSH NOTIFICATION SENT", [
            'identifier' => $identifier,
            'alert_type' => $alertType,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ]);

        // Store in database for debugging
        \DB::table('notifications_log')->insert([
            'type' => 'push',
            'recipient' => $identifier,
            'message' => json_encode($data),
            'status' => 'logged',
            'created_at' => now(),
        ]);
    }

    /**
     * Build alert message based on type
     */
    private function buildAlertMessage(string $alertType, array $data): string
    {
        $userName = $data['user_name'] ?? 'User';
        $location = $data['maps_link'] ?? 'Location unavailable';
        $battery = $data['battery_level'] ?? 'Unknown';
        $startTime = $data['start_time'] ?? 'Unknown';

        switch ($alertType) {
            case 'duress':
                return "🚨 EMERGENCY ALERT 🚨\n\n"
                    . "{$userName} has activated their DURESS signal!\n\n"
                    . "This means they are in danger and may be under threat.\n\n"
                    . "Trip started: {$startTime}\n"
                    . "Destination: " . ($data['destination'] ?? 'Not specified') . "\n\n"
                    . "📍 Last known location:\n{$location}\n\n"
                    . "🔋 Battery level: {$battery}%\n\n"
                    . "⚠️ IMMEDIATE ACTION REQUIRED:\n"
                    . "1. Try to contact {$userName} immediately\n"
                    . "2. If no response, contact local authorities (113)\n"
                    . "3. Provide them with the location link above\n\n"
                    . "SafeTrek - Personal Safety App";

            case 'panic':
                return "🚨 PANIC ALERT 🚨\n\n"
                    . "{$userName} has pressed the PANIC BUTTON!\n\n"
                    . "This is an immediate emergency!\n\n"
                    . "📍 Current location:\n{$location}\n\n"
                    . "🔋 Battery level: {$battery}%\n\n"
                    . "⚠️ IMMEDIATE ACTION REQUIRED:\n"
                    . "1. Try to contact {$userName} NOW\n"
                    . "2. Contact local authorities (113) immediately\n"
                    . "3. Provide them with the location link above\n\n"
                    . "SafeTrek - Personal Safety App";

            case 'timer_expired':
                return "⚠️ SAFETY CHECK-IN MISSED ⚠️\n\n"
                    . "{$userName} started a safety trip and has NOT checked in as safe.\n\n"
                    . "Trip started: {$startTime}\n"
                    . "Expected end: " . ($data['expected_end_time'] ?? 'Unknown') . "\n"
                    . "Destination: " . ($data['destination'] ?? 'Not specified') . "\n\n"
                    . "📍 Last known location:\n{$location}\n\n"
                    . "🔋 Battery level: {$battery}%\n\n"
                    . "⚠️ ACTION REQUIRED:\n"
                    . "1. Try to contact {$userName}\n"
                    . "2. If no response after multiple attempts, contact authorities\n"
                    . "3. This could be a phone issue, but please verify their safety\n\n"
                    . "SafeTrek - Personal Safety App";

            default:
                return "🚨 EMERGENCY ALERT from SafeTrek\n\n"
                    . "{$userName} needs help!\n"
                    . "Location: {$location}\n"
                    . "Please check on them immediately.";
        }
    }
}
