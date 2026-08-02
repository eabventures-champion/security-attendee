<?php
namespace App\Enums;

enum AccessRole: string
{
    case GeneralAdmission = 'general_admission';
    case Vip = 'vip';
    case Vvip = 'vvip';
    case Speaker = 'speaker';
    case Exhibitor = 'exhibitor';
    case Sponsor = 'sponsor';
    case Staff = 'staff';
    case Volunteer = 'volunteer';
    case Media = 'media';
    case Organizer = 'organizer';
    case Security = 'security';

    public function label(): string
    {
        if ($this === self::Vvip) return 'VVIP';
        if ($this === self::GeneralAdmission) return 'General';
        return ucwords(str_replace('_', ' ', $this->value));
    }

    public function color(): string
    {
        return match ($this) {
            self::GeneralAdmission => 'gray',
            self::Vip => 'purple',
            self::Vvip => 'amber',
            self::Speaker => 'blue',
            self::Exhibitor => 'orange',
            self::Sponsor => 'yellow',
            self::Staff, self::Organizer => 'indigo',
            self::Volunteer => 'teal',
            self::Media => 'cyan',
            self::Security => 'red',
        };
    }

    public function badgeClass(): string
    {
        return 'bg-' . $this->color() . '-100 text-' . $this->color() . '-800';
    }

    public static function attendeeCases(): array
    {
        return array_filter(self::cases(), function ($role) {
            return !in_array($role, [self::Organizer, self::Staff, self::Volunteer, self::Security]);
        });
    }
}