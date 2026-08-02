<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Enums\ScanResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CheckIn extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'attendee_id', 'event_id', 'gate_id', 'qr_code_id', 'scanned_by',
        'device_id', 'scan_result', 'scanned_at', 'ip_address', 'user_agent',
        'latitude', 'longitude', 'metadata',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'scan_result' => ScanResult::class,
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
    public function gate(): BelongsTo
    {
        return $this->belongsTo(Gate::class);
    }

    /**
     * @return BelongsTo
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class);
    }

    /**
     * @return BelongsTo
     */
    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('scan_result', ScanResult::Granted);
    }

    public function scopeDenied(Builder $query): Builder
    {
        return $query->where('scan_result', '!=', ScanResult::Granted);
    }

    public function scopeForGate(Builder $query, $gateId): Builder
    {
        return $query->where('gate_id', $gateId);
    }
}
