<?php

namespace App\Models;

use App\Enums\NotificationChannel;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'attendee_id', 'event_id', 'user_id', 'channel',
        'type', 'status', 'sent_at', 'error_message', 'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'channel' => NotificationChannel::class,
        'type' => NotificationType::class,
        'metadata' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }

    /**
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
