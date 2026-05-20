<?php

namespace App\Http\Controllers;

use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Models\UnitModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $units = UnitModel::query()
            ->when($request->search, fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->orderBy('id')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $units
        ]);
    }

    public function store(StoreUnitRequest $request): JsonResponse
    {
        $unit = UnitModel::create($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $unit
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $unit = UnitModel::find($id);

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, unity with id {$id} cannot be found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $unit
        ]);
    }

    public function update(UpdateUnitRequest $request, int $id): JsonResponse
    {
        $unit = UnitModel::find($id);

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, unity with id {$id} cannot be found"
            ], 404);
        }

        $unit->update($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $unit
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $unit = UnitModel::find($id);

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, unity with id {$id} cannot be found"
            ], 404);
        }

        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit muvaffaqiyatli ochirildi'
        ]);
    }
}
