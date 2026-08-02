<?php

namespace App\Models;

use App\Enums\WaitingListStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaitingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'full_name', 'email', 'phone', 'position', 'notified_at', 'status',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'status' => WaitingListStatus::class,
    ];

    /**
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
