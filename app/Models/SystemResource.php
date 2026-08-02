<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class SystemResource extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'title',
        'content',
        'category',
        'priority',
        'is_published',
        'pinned',
        'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'pinned' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopePinnedFirst(Builder $query): Builder
    {
        return $query->orderBy('pinned', 'desc')->orderBy('created_at', 'desc');
    }
}
