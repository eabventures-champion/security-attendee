<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = Attendance::query()
            ->with(['attendee', 'gate', 'scannedBy'])
            ->when($request->event_id, fn($q) => $q->where('event_id', $request->event_id))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $records,
            'message' => 'Attendance records retrieved'
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $eventId = $request->validate(['event_id' => 'required|exists:events,id'])['event_id'];

        $totalCheckedIn = Attendance::where('event_id', $eventId)->count();
        $uniqueCheckedIn = Attendance::where('event_id', $eventId)->distinct('attendee_id')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_scans' => $totalCheckedIn,
                'unique_attendees' => $uniqueCheckedIn,
            ],
            'message' => 'Stats retrieved'
        ]);
    }
}
