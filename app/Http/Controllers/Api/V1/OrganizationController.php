<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $organizations = Organization::paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $organizations,
            'message' => 'Organizations retrieved'
        ]);
    }

    public function show(Organization $organization): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $organization,
            'message' => 'Organization retrieved'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_color' => 'nullable|string|max:20',
        ]);

        $organization = Organization::create($validated);

        return response()->json([
            'success' => true,
            'data' => $organization,
            'message' => 'Organization created'
        ], 201);
    }

    public function update(Request $request, Organization $organization): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'brand_color' => 'nullable|string|max:20',
        ]);

        $organization->update($validated);

        return response()->json([
            'success' => true,
            'data' => $organization,
            'message' => 'Organization updated'
        ]);
    }

    public function destroy(Organization $organization): JsonResponse
    {
        $organization->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Organization deleted'
        ]);
    }
}
