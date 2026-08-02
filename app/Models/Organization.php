<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;

class Organization extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'logo_path', 'brand_color',
        'timezone', 'website', 'phone', 'address', 'settings', 'is_active',
        'approval_status', 'approved_at', 'has_premium_typography',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'has_premium_typography' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(OrganizationSubscription::class);
    }

    /**
     * @return HasOne
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(OrganizationSubscription::class)->where('status', \App\Enums\SubscriptionStatus::Active);
    }

    /**
     * @return HasManyThrough
     */
    public function attendees(): HasManyThrough
    {
        return $this->hasManyThrough(Attendee::class, Event::class);
    }

    /**
     * @return HasMany
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * @return HasMany
     */
    public function scanDevices(): HasMany
    {
        return $this->hasMany(ScanDevice::class);
    }

    /**
     * Scope a query to only include active organizations.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the organization's logo URL.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }
}
