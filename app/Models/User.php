<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasUuid;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuid, SoftDeletes;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar_path',
        'organization_id',
        'assigned_gate_id',
        'invitation_token',
        'invitation_status',
        'invitation_accepted_at',
        'approval_status',
        'approval_token',
        'approved_at',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function assignedGate(): BelongsTo
    {
        return $this->belongsTo(Gate::class, 'assigned_gate_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'invitation_accepted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function isApproved(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->approval_status === 'approved' && $this->is_active;
    }

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
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class, 'scanned_by');
    }

    /**
     * @return HasMany
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForOrganization(Builder $query, $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin') || $this->hasRole('super-admin');
    }

    public function getRoleLabelAttribute(): string
    {
        $role = $this->roles->first()?->name;

        return match ($role) {
            'super_admin', 'super-admin' => 'Super Admin',
            'organization_admin' => 'Organization Admin',
            'event_manager' => 'Event Manager',
            'security_officer', 'gate_staff', 'security' => 'Security Officer',
            'volunteer' => 'Volunteer',
            'scanner_operator' => 'Scanner Operator',
            'attendee' => 'Attendee',
            default => ucwords(str_replace(['_', '-'], ' ', $role ?? 'Organization Admin')),
        };
    }

    public function getRoleColorAttribute(): string
    {
        $role = $this->roles->first()?->name;

        return match ($role) {
            'super_admin', 'super-admin' => 'purple',
            'organization_admin' => 'emerald',
            'event_manager' => 'blue',
            'security_officer', 'gate_staff', 'security' => 'amber',
            'volunteer' => 'purple',
            'scanner_operator' => 'blue',
            default => 'emerald',
        };
    }

    public function isOrganizationAdmin(): bool
    {
        return $this->hasRole('org-admin') || $this->hasRole('organization_admin');
    }

    public function isSecurityPersonnel(): bool
    {
        if ($this->hasRole('gate_staff') || $this->hasRole('security') || $this->hasRole('gate-security') || $this->hasRole('security_officer')) {
            return true;
        }

        return Attendee::where('email', $this->email)
            ->where('access_role', \App\Enums\AccessRole::Security)
            ->exists();
    }

    public function assignedGateForEvent($eventId = null): ?Gate
    {
        if ($this->assigned_gate_id) {
            $gate = Gate::find($this->assigned_gate_id);
            if ($gate && (!$eventId || $gate->event_id == $eventId)) {
                return $gate;
            }
        }

        $attendeeQuery = Attendee::where('email', $this->email)
            ->whereNotNull('assigned_gate_id');

        if ($eventId) {
            $attendeeQuery->where('event_id', $eventId);
        }

        $attendee = $attendeeQuery->latest()->first();

        return $attendee ? $attendee->assignedGate : null;
    }

    public function belongsToOrganization(Organization $organization): bool
    {
        return $this->organization_id === $organization->id;
    }
}
