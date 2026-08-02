<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendee;
use App\Enums\VerificationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $attendee = Attendee::where('verification_token', $request->token)
            ->where('verification_status', VerificationStatus::Pending)
            ->first();

        if (!$attendee) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Invalid or expired verification token'
            ], 400);
        }

        $attendee->update([
            'verification_status' => VerificationStatus::Verified,
            'verified_at' => now(),
            'verification_token' => null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $attendee,
            'message' => 'Verification successful'
        ]);
    }
}
