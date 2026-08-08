<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToOrganization;
use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use HasFactory, HasUuid, SoftDeletes, BelongsToOrganization;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($event) {
            if (method_exists($event, 'isForceDeleting') && $event->isForceDeleting()) {
                $event->attendees()->forceDelete();
                $event->gates()->forceDelete();
                $event->qrCodes()->forceDelete();
                $event->checkIns()->forceDelete();
            } else {
                $event->attendees()->delete();
                $event->gates()->delete();
                $event->qrCodes()->delete();
                $event->checkIns()->delete();
            }
        });
    }

    protected $fillable = [
        'name', 'slug', 'description', 'invitation_title', 'invitation_description', 'title_font', 'venue_name', 'venue_address',
        'venue_city', 'venue_country', 'venue_latitude', 'venue_longitude',
        'starts_at', 'ends_at', 'registration_opens_at', 'registration_deadline',
        'capacity', 'status', 'settings', 'is_multi_day', 'is_free', 'is_private',
        'published_at', 'cancelled_at', 'cancelled_reason', 'cover_image_path',
        'organization_id',
    ];

    public function getTitleCssFontFamilyAttribute(): string
    {
        return match($this->title_font) {
            'Outfit' => "'Outfit', sans-serif",
            'Playfair Display' => "'Playfair Display', serif",
            'Cinzel' => "'Cinzel', serif",
            'Space Grotesk' => "'Space Grotesk', sans-serif",
            default => "'Alex Brush', cursive",
        };
    }

    public function getFullVenueLocationAttribute(): string
    {
        $parts = array_filter([
            $this->venue_name,
            $this->venue_address,
            $this->venue_city,
            $this->venue_country,
        ], fn($val) => !empty(trim((string)$val)));

        return !empty($parts) ? implode(', ', $parts) : 'Location TBA';
    }

    public static function defaultFormFieldsConfig(): array
    {
        return [
            'standard_fields' => [
                'full_name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'company' => 'optional',
                'job_title' => 'optional',
                'country' => 'disabled',
                'gender' => 'disabled',
                'emergency_contact_name' => 'disabled',
                'emergency_contact_phone' => 'disabled',
                'dietary_preferences' => 'disabled',
                'accessibility_needs' => 'disabled',
                'registration_reason' => 'optional',
            ],
            'custom_fields' => [],
        ];
    }

    public function getFormFieldsConfigAttribute(): array
    {
        $default = static::defaultFormFieldsConfig();
        $stored = is_array($this->settings) && isset($this->settings['form_fields']) ? $this->settings['form_fields'] : [];

        return [
            'standard_fields' => array_merge(
                $default['standard_fields'],
                is_array($stored['standard_fields'] ?? null) ? $stored['standard_fields'] : []
            ),
            'custom_fields' => is_array($stored['custom_fields'] ?? null) ? $stored['custom_fields'] : [],
        ];
    }

    public function getDefaultEntryModeAttribute(): string
    {
        return is_array($this->settings) && isset($this->settings['default_entry_mode'])
            ? $this->settings['default_entry_mode']
            : 'details';
    }

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'registration_opens_at' => 'datetime',
        'registration_deadline' => 'datetime',
        'published_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'status' => EventStatus::class,
        'settings' => 'array',
        'is_multi_day' => 'boolean',
        'is_free' => 'boolean',
        'is_private' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return HasMany
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(EventSession::class);
    }

    /**
     * @return HasMany
     */
    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class);
    }

    /**
     * @return HasMany
     */
    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(EventInvitation::class);
    }

    /**
     * @return HasMany
     */
    public function gates(): HasMany
    {
        return $this->hasMany(Gate::class);
    }

    /**
     * @return HasMany
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * @return HasMany
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * @return HasMany
     */
    public function waitingList(): HasMany
    {
        return $this->hasMany(WaitingList::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', EventStatus::Published)->whereNotNull('published_at');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('ends_at', '<', now());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', EventStatus::Published)
                     ->whereNull('cancelled_at');
    }

    public function getIsRegistrationOpenAttribute(): bool
    {
        $now = now();
        return $this->status === EventStatus::Published &&
               (!$this->registration_opens_at || $this->registration_opens_at->isPast()) &&
               (!$this->registration_deadline || $this->registration_deadline->isFuture());
    }

    public function getIsFullAttribute(): bool
    {
        if ($this->capacity === null) {
            return false;
        }
        return $this->attendees()->count() >= $this->capacity;
    }

    public function getRemainingCapacityAttribute(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }
        return max(0, $this->capacity - $this->attendees()->count());
    }

    public function getAttendeeCountAttribute(): int
    {
        return $this->attendees()->count();
    }

    public function getVerifiedAttendeeCountAttribute(): int
    {
        return $this->attendees()->verified()->count();
    }

    public function getCheckedInCountAttribute(): int
    {
        return $this->checkIns()->successful()->count();
    }

    public function getIsVenueCapacityReachedAttribute(): bool
    {
        if ($this->capacity === null || $this->capacity <= 0) {
            return false;
        }
        return $this->checked_in_count >= $this->capacity;
    }

    public function publish(): void
    {
        $this->update([
            'status' => EventStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => EventStatus::Cancelled,
            'cancelled_at' => now(),
            'cancelled_reason' => $reason,
        ]);
    }

    public function archive(): void
    {
        $this->update([
            'status' => EventStatus::Archived,
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => EventStatus::Completed,
        ]);
    }

    public function duplicate(): self
    {
        $clone = $this->replicate();
        $clone->name = $this->name . ' (Copy)';
        $clone->slug = $this->slug . '-copy';
        $clone->status = EventStatus::Draft;
        $clone->published_at = null;
        $clone->cancelled_at = null;
        $clone->cancelled_reason = null;
        $clone->save();
        return $clone;
    }
}
