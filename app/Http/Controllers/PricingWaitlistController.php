<?php

namespace App\Http\Controllers;

use App\Models\PricingWaitlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PricingWaitlistController extends Controller
{
    /**
     * Store a new VIP pricing waitlist email.
     */
    public function store(Request $request): JsonResponse
    {
        $rawEmail = $request->input('email') ?? $request->json('email');
        $email = strtolower(trim((string)$rawEmail));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address.',
            ], 422);
        }

        $waitlist = PricingWaitlist::firstOrCreate(
            ['email' => $email],
            [
                'ip_address' => $request->ip(),
                'status' => 'pending',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "You're on the VIP list! We'll send your launch invitation and 50% discount code first.",
            'already_registered' => !$waitlist->wasRecentlyCreated,
        ]);
    }
}
