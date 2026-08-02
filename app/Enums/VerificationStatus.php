<?php
namespace App\Enums;

enum VerificationStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Verified => 'Verified',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Verified => 'emerald',
            self::Rejected => 'rose',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'clock',
            self::Verified => 'check',
            self::Rejected => 'x',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::Verified => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::Rejected => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
        };
    }
}