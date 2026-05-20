<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Models\LocationModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locations = LocationModel::query()
            ->when($request->search, fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->orderBy('id')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $locations
        ]);
    }

    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = LocationModel::create($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $location
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $location = LocationModel::find($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, location with id {$id} cannot be found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $location
        ]);
    }

    public function update(UpdateLocationRequest $request, int $id): JsonResponse
    {
        $location = LocationModel::find($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, location with id {$id} cannot be found"
            ], 404);
        }

        $location->update($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $location
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $location = LocationModel::find($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, location with id {$id} cannot be found"
            ], 404);
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location muvaffaqiyatli ochirildi'
        ]);
    }
}
