<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanDevice extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'event_id', 'organization_id', 'device_name', 'device_identifier',
        'device_token', 'is_authorized', 'authorized_by', 'authorized_at',
        'last_active_at', 'metadata',
    ];

    protected $casts = [
        'is_authorized' => 'boolean',
        'authorized_at' => 'datetime',
        'last_active_at' => 'datetime',
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
    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }
}
