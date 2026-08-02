<?php
namespace App\Enums;

enum ScanResult: string
{
    case Granted = 'granted';
    case DeniedWrongGate = 'denied_wrong_gate';
    case DeniedAlreadyCheckedIn = 'denied_already_checked_in';
    case DeniedQrExpired = 'denied_qr_expired';
    case DeniedNotVerified = 'denied_not_verified';
    case DeniedRevoked = 'denied_revoked';
    case DeniedUnauthorized = 'denied_unauthorized';
    case DeniedInvalid = 'denied_invalid';
    case DeniedDeviceUnauthorized = 'denied_device_unauthorized';

    public function label(): string
    {
        return match ($this) {
            self::Granted => 'Access Granted',
            self::DeniedWrongGate => 'Wrong Gate',
            self::DeniedAlreadyCheckedIn => 'Already Checked In',
            self::DeniedQrExpired => 'QR Code Expired',
            self::DeniedNotVerified => 'Not Verified',
            self::DeniedRevoked => 'Access Revoked',
            self::DeniedUnauthorized => 'Unauthorized',
            self::DeniedInvalid => 'Invalid QR Code',
            self::DeniedDeviceUnauthorized => 'Device Unauthorized',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Granted => 'emerald',
            default => 'rose',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Granted => 'check-circle',
            default => 'x-circle',
        };
    }

    public function isSuccess(): bool
    {
        return $this === self::Granted;
    }
}