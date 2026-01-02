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
        $userName = $data['user_name'] ?? 'Người dùng';
        $location = $data['maps_link'] ?? 'Vị trí không xác định';
        $battery = $data['battery_level'] ?? 'Không rõ';
        $startTime = $data['start_time'] ?? 'Không rõ';

        switch ($alertType) {
            case 'duress':
                return "🚨 CẢNH BÁO KHẨN CẤP 🚨\n\n"
                    . "{$userName} đã kích hoạt tín hiệu BỊ ÉP BUỘC!\n\n"
                    . "Điều này có nghĩa họ đang gặp nguy hiểm và có thể đang bị đe dọa.\n\n"
                    . "Bắt đầu chuyến đi: {$startTime}\n"
                    . "Điểm đến: " . ($data['destination'] ?? 'Không xác định') . "\n\n"
                    . "📍 Vị trí cuối cùng:\n{$location}\n\n"
                    . "🔋 Mức pin: {$battery}%\n\n"
                    . "⚠️ CẦN HÀNH ĐỘNG NGAY LẬP TỨC:\n"
                    . "1. Liên hệ với {$userName} NGAY\n"
                    . "2. Nếu không liên lạc được, gọi cơ quan chức năng (113)\n"
                    . "3. Cung cấp cho họ link vị trí ở trên\n\n"
                    . "SafeTrek - Ứng dụng An toàn Cá nhân";

            case 'panic':
                return "🚨 CẢNH BÁO HOẢNG LOẠN 🚨\n\n"
                    . "{$userName} đã nhấn NÚT HOẢNG LOẠN!\n\n"
                    . "Đây là tình huống khẩn cấp ngay lập tức!\n\n"
                    . "📍 Vị trí hiện tại:\n{$location}\n\n"
                    . "🔋 Mức pin: {$battery}%\n\n"
                    . "⚠️ CẦN HÀNH ĐỘNG NGAY LẬP TỨC:\n"
                    . "1. Liên hệ với {$userName} NGAY BÂY GIỜ\n"
                    . "2. Gọi cơ quan chức năng (113) ngay lập tức\n"
                    . "3. Cung cấp cho họ link vị trí ở trên\n\n"
                    . "SafeTrek - Ứng dụng An toàn Cá nhân";

            case 'timer_expired':
                return "⚠️ KHÔNG NHẬN ĐƯỢC XÁC NHẬN AN TOÀN ⚠️\n\n"
                    . "{$userName} đã bắt đầu chuyến đi an toàn nhưng CHƯA xác nhận an toàn.\n\n"
                    . "Bắt đầu chuyến đi: {$startTime}\n"
                    . "Dự kiến kết thúc: " . ($data['expected_end_time'] ?? 'Không rõ') . "\n"
                    . "Điểm đến: " . ($data['destination'] ?? 'Không xác định') . "\n\n"
                    . "📍 Vị trí cuối cùng:\n{$location}\n\n"
                    . "🔋 Mức pin: {$battery}%\n\n"
                    . "⚠️ HÀNH ĐỘNG CẦN THIẾT:\n"
                    . "1. Cố gắng liên hệ với {$userName}\n"
                    . "2. Nếu không liên lạc được sau nhiều lần thử, liên hệ cơ quan chức năng\n"
                    . "3. Có thể do sự cố điện thoại, nhưng vui lòng kiểm tra an toàn của họ\n\n"
                    . "SafeTrek - Ứng dụng An toàn Cá nhân";

            default:
                return "🚨 CẢNH BÁO KHẨN CẤP từ SafeTrek\n\n"
                    . "{$userName} cần giúp đỡ!\n"
                    . "Vị trí: {$location}\n"
                    . "Vui lòng kiểm tra họ ngay lập tức.";
        }
    }
}
