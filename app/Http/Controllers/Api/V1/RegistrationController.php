<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    public function store(Request $request, string $eventUuid): JsonResponse
    {
        $event = Event::where('uuid', $eventUuid)->firstOrFail();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        try {
            $attendee = $this->registrationService->register($event, $validated);

            return response()->json([
                'success' => true,
                'data' => $attendee,
                'message' => 'Registration successful'
            ], 201);
        } catch (\Exception $e) {
            // Assume conflict for duplicate email in simple implementation
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage()
            ], 409);
        }
    }
}
