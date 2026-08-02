<?php
namespace App\Enums;

enum AuditAction: string
{
    case Registration = 'registration';
    case Verification = 'verification';
    case QrGeneration = 'qr_generation';
    case QrRevocation = 'qr_revocation';
    case CheckIn = 'check_in';
    case DuplicateScan = 'duplicate_scan';
    case ManualOverride = 'manual_override';
    case Login = 'login';
    case Logout = 'logout';
    case PermissionChange = 'permission_change';
    case EventCreated = 'event_created';
    case EventUpdated = 'event_updated';
    case EventPublished = 'event_published';
    case EventCancelled = 'event_cancelled';
    case GateCreated = 'gate_created';
    case SettingsUpdated = 'settings_updated';
}