<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Enums\AccessRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gate extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'event_id', 'name', 'description', 'location', 'is_active', 'allowed_roles', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allowed_roles' => 'array',
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
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * @return HasMany
     */
    public function assignedSecurityUsers(): HasMany
    {
        return $this->hasMany(User::class, 'assigned_gate_id');
    }

    /**
     * @return HasMany
     */
    public function assignedSecurityAttendees(): HasMany
    {
        return $this->hasMany(Attendee::class, 'assigned_gate_id');
    }

    public function allowsRole(AccessRole $role): bool
    {
        if (empty($this->allowed_roles)) {
            return true;
        }

        return in_array($role->value, $this->allowed_roles);
    }

    public function isOpen(): bool
    {
        return $this->is_active;
    }
}
