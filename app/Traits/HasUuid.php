<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function initializeHasUuid(): void
    {
        $this->fillable = array_diff($this->fillable, ['uuid']);
        $this->guarded[] = 'uuid';
    }

    public static function findByUuid(string $uuid): ?self
    {
        return static::where('uuid', $uuid)->first();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}