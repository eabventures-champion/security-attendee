<?php
namespace App\Services;

use App\Enums\NotificationType;
use App\Enums\NotificationChannel;
use Illuminate\Support\Collection;

class NotificationService
{
    public function send($attendee, NotificationType $type, NotificationChannel $channel): void
    {
        // Implementation
    }

    public function sendBulk(Collection $attendees, NotificationType $type): void
    {
        // Implementation
    }

    public function logNotification($attendee, NotificationType $type, NotificationChannel $channel, string $status): void
    {
        // Implementation
    }
}