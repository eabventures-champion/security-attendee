<?php
namespace App\Enums;

enum EventStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Cancelled => 'Cancelled',
            self::Completed => 'Completed',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'emerald',
            self::Cancelled => 'rose',
            self::Completed => 'blue',
            self::Archived => 'amber',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'pencil',
            self::Published => 'check-circle',
            self::Cancelled => 'x-circle',
            self::Completed => 'flag',
            self::Archived => 'archive',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
            self::Published => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::Cancelled => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            self::Completed => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
            self::Archived => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
        };
    }
}