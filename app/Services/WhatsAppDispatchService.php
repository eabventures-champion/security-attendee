<?php

namespace App\Services;

use App\Models\Attendee;
use App\Models\NotificationLog;
use App\Enums\NotificationChannel;
use App\Enums\NotificationType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WhatsAppDispatchService
{
    /**
     * Dispatch WhatsApp QR Pass message to attendee and log delivery status.
     *
     * @param Attendee $attendee
     * @return array ['success' => bool, 'status' => string, 'url' => string, 'message' => string]
     */
    public static function dispatchQrPass(Attendee $attendee): array
    {
        if (!$attendee->relationLoaded('qrCode')) {
            $attendee->load('qrCode');
        }
        if (!$attendee->relationLoaded('event')) {
            $attendee->load('event');
        }

        $qrCodeToken = $attendee->qrCode ? $attendee->qrCode->secure_token : null;
        if (!$qrCodeToken) {
            return [
                'success' => false,
                'status' => 'failed',
                'url' => '',
                'message' => 'No active QR pass generated for attendee.',
            ];
        }

        $rawPhone = $attendee->phone;
        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$rawPhone);

        // Standardize local Ghana number (e.g. 0547977840 -> 233547977840, or 547977840 from Excel -> 233547977840)
        if (strlen($cleanPhone) === 9) {
            $cleanPhone = '233' . $cleanPhone;
        } elseif (!empty($cleanPhone) && str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '233' . substr($cleanPhone, 1);
        }

        $eventName = $attendee->event ? $attendee->event->name : 'Event';
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=" . urlencode($qrCodeToken);

        $messageText = "Hello {$attendee->full_name},\n\nHere is your official digital entry pass for *{$eventName}*:\n\n🎟️ *Pass Token ID:* {$qrCodeToken}\n\n📷 *View/Download Your Entry Pass QR Code:* \n{$qrImageUrl}\n\nPlease present this QR code at check-in.";

        // Valid Ghana mobile network prefixes (MTN, Telecel, AirtelTigo)
        // MTN: 24, 54, 55, 59, 53 | Telecel: 20, 50 | AirtelTigo: 27, 57, 26, 56
        $validGhanaMobilePrefixes = ['24', '54', '55', '59', '53', '20', '50', '27', '57', '26', '56'];

        // Validate basic phone length (10-15 digits)
        $isCompliant = !empty($cleanPhone) && strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 15;
        $errorMessage = null;

        // If Ghana number (233XXXXXXXXX = 12 digits), verify it has a valid mobile network prefix
        if ($isCompliant && str_starts_with($cleanPhone, '233') && strlen($cleanPhone) === 12) {
            $mobilePrefix = substr($cleanPhone, 3, 2);
            if (!in_array($mobilePrefix, $validGhanaMobilePrefixes)) {
                $isCompliant = false;
                $errorMessage = "Number +{$cleanPhone} uses prefix '0{$mobilePrefix}', which is not a valid WhatsApp mobile prefix in Ghana.";
            }
        } elseif (!$isCompliant) {
            $errorMessage = 'Phone number is missing or non-WhatsApp compliant.';
        }

        if ($isCompliant) {
            $whatsappUrl = "https://api.whatsapp.com/send?phone={$cleanPhone}&text=" . rawurlencode($messageText);
            $status = 'delivered';
        } else {
            $whatsappUrl = "https://api.whatsapp.com/send?text=" . rawurlencode($messageText);
            $status = 'failed';
        }

        // Log to notification_logs table
        try {
            NotificationLog::create([
                'uuid' => (string) Str::uuid(),
                'attendee_id' => $attendee->id,
                'event_id' => $attendee->event_id,
                'user_id' => auth()->id(),
                'channel' => NotificationChannel::WhatsApp->value,
                'type' => NotificationType::QrDelivery->value,
                'status' => $status,
                'sent_at' => $isCompliant ? now() : null,
                'error_message' => $errorMessage,
                'metadata' => [
                    'recipient_phone' => $cleanPhone ?: $rawPhone,
                    'is_compliant' => $isCompliant,
                    'whatsapp_url' => $whatsappUrl,
                    'token' => $qrCodeToken,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to write WhatsApp NotificationLog: ' . $e->getMessage());
        }

        return [
            'success' => $isCompliant,
            'status' => $status,
            'url' => $whatsappUrl,
            'message' => $messageText,
        ];
    }
}
