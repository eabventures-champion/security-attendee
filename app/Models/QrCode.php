<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class QrCode extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'attendee_id', 'event_id', 'secure_token', 'encrypted_payload',
        'digital_signature', 'qr_image_path', 'expires_at', 'is_revoked',
        'issued_at', 'revoked_at', 'revoked_reason', 'reissue_count', 'last_scanned_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_scanned_at' => 'datetime',
        'is_revoked' => 'boolean',
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
     * @return HasMany
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_revoked', false)
                     ->where(function (Builder $q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function scopeRevoked(Builder $query): Builder
    {
        return $query->where('is_revoked', true);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
                     ->where('expires_at', '<=', now());
    }

    public function isValid(): bool
    {
        return !$this->isRevoked() && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->is_revoked;
    }

    public function revoke(string $reason = null): void
    {
        $this->update([
            'is_revoked' => true,
            'revoked_at' => now(),
            'revoked_reason' => $reason,
        ]);
    }

    public function markScanned(): void
    {
        $this->update([
            'last_scanned_at' => now(),
        ]);
    }
}
