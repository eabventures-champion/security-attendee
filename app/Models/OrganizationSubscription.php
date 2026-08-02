<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class OrganizationSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'plan_id', 'status', 'starts_at', 'ends_at',
        'trial_ends_at', 'events_used', 'registrations_used',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'status' => SubscriptionStatus::class,
    ];

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Check if the subscription is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::Active && !$this->isExpired();
    }

    /**
     * Check if the subscription is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    /**
     * Check if the subscription is currently on trial.
     *
     * @return bool
     */
    public function isTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if the organization can create another event.
     *
     * @return bool
     */
    public function canCreateEvent(): bool
    {
        if ($this->plan->isUnlimited('max_events')) {
            return true;
        }

        return $this->events_used < $this->plan->max_events;
    }

    /**
     * Check if the organization can register another attendee.
     *
     * @return bool
     */
    public function canRegisterAttendee(): bool
    {
        if ($this->plan->isUnlimited('max_registrations')) {
            return true;
        }

        return $this->registrations_used < $this->plan->max_registrations;
    }

    /**
     * Increment the number of events used.
     *
     * @return void
     */
    public function incrementEventsUsed(): void
    {
        $this->increment('events_used');
    }

    /**
     * Increment the number of registrations used.
     *
     * @return void
     */
    public function incrementRegistrationsUsed(): void
    {
        $this->increment('registrations_used');
    }
}
