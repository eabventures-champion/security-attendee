<?php
namespace App\Enums;

enum NotificationType: string
{
    case RegistrationConfirmation = 'registration_confirmation';
    case VerificationRequest = 'verification_request';
    case VerificationSuccess = 'verification_success';
    case QrDelivery = 'qr_delivery';
    case EventReminder = 'event_reminder';
    case CheckinConfirmation = 'checkin_confirmation';
    case ThankYou = 'thank_you';
}