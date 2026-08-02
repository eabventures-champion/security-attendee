<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendee;
use App\Services\CheckInService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ScanController extends Controller
{
    public function __construct(
        private CheckInService $checkInService
    ) {}

    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string',
            'gate_id' => 'required|exists:gates,id',
        ]);

        try {
            $result = $this->checkInService->processScan($request->qr_data, $request->gate_id, auth()->id());

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Scan processed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function manualCheckIn(Request $request, string $attendeeUuid): JsonResponse
    {
        $request->validate([
            'gate_id' => 'required|exists:gates,id',
        ]);

        $attendee = Attendee::where('uuid', $attendeeUuid)->firstOrFail();

        try {
            $result = $this->checkInService->manualCheckIn($attendee, $request->gate_id, auth()->id());

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Manual check-in successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
