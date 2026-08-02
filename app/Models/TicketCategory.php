<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Enums\AccessRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'event_id', 'name', 'description', 'capacity', 'price',
        'access_role', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'access_role' => AccessRole::class,
    ];

    /**
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return HasMany
     */
    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    /**
     * Check if the category is full.
     *
     * @return bool
     */
    public function isFull(): bool
    {
        if ($this->capacity === null) {
            return false;
        }

        return $this->attendees()->count() >= $this->capacity;
    }

    /**
     * Get the remaining capacity.
     *
     * @return int|null
     */
    public function remainingCapacity(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }

        return max(0, $this->capacity - $this->attendees()->count());
    }
}
