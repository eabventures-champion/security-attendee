<?php
namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                if ($user && $user->isSuperAdmin()) {
                    return;
                }
                if (session()->has('current_organization_id')) {
                    $builder->where($builder->getModel()->getTable() . '.organization_id', session('current_organization_id'));
                }
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->organization_id) && auth()->check() && session()->has('current_organization_id')) {
                $model->organization_id = session('current_organization_id');
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeForOrganization(Builder $query, int $organizationId): Builder
    {
        return $query->withoutGlobalScope('organization')->where('organization_id', $organizationId);
    }
}