<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'max_events', 'max_registrations', 'features',
        'price_monthly', 'price_yearly', 'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
    ];

    /**
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(OrganizationSubscription::class, 'plan_id');
    }

    /**
     * Check if a field is unlimited (-1).
     *
     * @param string $field
     * @return bool
     */
    public function isUnlimited(string $field): bool
    {
        return $this->{$field} === -1;
    }

    /**
     * Check if the plan has a specific feature.
     *
     * @param string $feature
     * @return bool
     */
    public function hasFeature(string $feature): bool
    {
        if (!is_array($this->features)) {
            return false;
        }

        return in_array($feature, $this->features);
    }
}
