<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToOrganization;
use App\Enums\AccessRole;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Attendee extends Model
{
    use HasFactory, HasUuid, SoftDeletes, BelongsToOrganization;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attendee) {
            $attendee->checkIns()->delete();
            if ($attendee->qrCode) {
                $attendee->qrCode()->delete();
            }
        });
    }

    protected $fillable = [
        'event_id', 'organization_id', 'user_id', 'assigned_gate_id', 'ticket_category_id', 'full_name', 'email',
        'phone', 'company', 'job_title', 'country', 'gender', 'emergency_contact_name',
        'emergency_contact_phone', 'dietary_preferences', 'accessibility_needs',
        'consent', 'access_role', 'verification_status', 'verification_token',
        'verification_code', 'verified_at', 'registration_ip', 'notes', 'registration_reason', 'metadata',
    ];

    protected $casts = [
        'consent' => 'boolean',
        'verified_at' => 'datetime',
        'verification_status' => VerificationStatus::class,
        'access_role' => AccessRole::class,
        'metadata' => 'array',
    ];

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
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo
     */
    public function assignedGate(): BelongsTo
    {
        return $this->belongsTo(Gate::class, 'assigned_gate_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function ticketCategory(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    /**
     * @return HasOne
     */
    public function qrCode(): HasOne
    {
        return $this->hasOne(QrCode::class);
    }

    /**
     * @return HasMany
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * @return HasOne
     */
    public function latestCheckIn(): HasOne
    {
        return $this->hasOne(CheckIn::class)->latestOfMany();
    }

    /**
     * @return HasMany
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * @return HasOne
     */
    public function waitingListEntry(): HasOne
    {
        return $this->hasOne(WaitingList::class, 'email', 'email')->where('event_id', $this->event_id);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verification_status', VerificationStatus::Verified);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('verification_status', VerificationStatus::Pending);
    }

    public function scopeCheckedIn(Builder $query): Builder
    {
        return $query->whereHas('checkIns', function (Builder $q) {
            $q->where('scan_result', \App\Enums\ScanResult::Granted);
        });
    }

    public function scopeNotCheckedIn(Builder $query): Builder
    {
        return $query->whereDoesntHave('checkIns', function (Builder $q) {
            $q->where('scan_result', \App\Enums\ScanResult::Granted);
        });
    }

    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::Verified;
    }

    public function isCheckedIn(): bool
    {
        return $this->checkIns()->where('scan_result', \App\Enums\ScanResult::Granted)->exists();
    }

    public function hasActiveQrCode(): bool
    {
        return $this->qrCode && $this->qrCode->isValid();
    }

    public function markAsVerified(): void
    {
        $this->update([
            'verification_status' => VerificationStatus::Verified,
            'verified_at' => now(),
        ]);
    }

    public function getQrCode(): ?QrCode
    {
        return $this->qrCode;
    }
}
