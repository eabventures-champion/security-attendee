<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function show(Request $request, string $type): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|exists:events,id'
        ]);

        $data = $this->reportService->generate($type, $request->event_id);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Report generated'
        ]);
    }

    public function export(Request $request, string $type)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        return $this->reportService->export($type, $request->event_id, $request->format);
    }
}
