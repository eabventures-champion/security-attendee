<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(
        private EventService $eventService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search', 'organization_id']);
        $events = Event::query()
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['organization_id']), fn($q) => $q->where('organization_id', $filters['organization_id']))
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $events,
            'message' => 'Events retrieved successfully'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'venue' => 'nullable|string',
            'capacity' => 'nullable|integer',
        ]);

        $event = $this->eventService->create($validated);

        return response()->json([
            'success' => true,
            'data' => $event,
            'message' => 'Event created successfully'
        ], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $event,
            'message' => 'Event retrieved successfully'
        ]);
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'venue' => 'nullable|string',
            'capacity' => 'nullable|integer',
        ]);

        $event = $this->eventService->update($event, $validated);

        return response()->json([
            'success' => true,
            'data' => $event,
            'message' => 'Event updated successfully'
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $this->eventService->delete($event);

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Event deleted successfully'
        ]);
    }

    public function publish(string $uuid): JsonResponse
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $this->eventService->publish($event);

        return response()->json([
            'success' => true,
            'data' => $event,
            'message' => 'Event published successfully'
        ]);
    }

    public function cancel(string $uuid): JsonResponse
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $this->eventService->cancel($event);

        return response()->json([
            'success' => true,
            'data' => $event,
            'message' => 'Event cancelled successfully'
        ]);
    }

    public function duplicate(string $uuid): JsonResponse
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $newEvent = $this->eventService->duplicate($event);

        return response()->json([
            'success' => true,
            'data' => $newEvent,
            'message' => 'Event duplicated successfully'
        ], 201);
    }
}
