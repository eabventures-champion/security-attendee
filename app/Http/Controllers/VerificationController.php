<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Services\VerificationService;
use App\Services\QrCodeService;
use App\Services\AuditService;
use App\Enums\VerificationStatus;
use App\Enums\AuditAction;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(
        private VerificationService $verificationService,
        private QrCodeService $qrCodeService,
        private AuditService $auditService,
    ) {}

    public function verify(string $token)
    {
        $attendee = Attendee::where('verification_token', $token)
            ->where('verification_status', VerificationStatus::Pending)
            ->first();

        if (!$attendee) {
            return view('verification.invalid');
        }

        // Verify the attendee
        $attendee->update([
            'verification_status' => VerificationStatus::Verified,
            'verified_at' => now(),
            'verification_token' => null,
        ]);

        // Generate QR code
        $qrCode = $this->qrCodeService->generate($attendee);

        // Log the verification
        $this->auditService->log(
            AuditAction::Verification,
            "Attendee {$attendee->full_name} verified via email",
            $attendee
        );

        return view('verification.success', [
            'attendee' => $attendee,
            'event' => $attendee->event,
        ]);
    }
}
