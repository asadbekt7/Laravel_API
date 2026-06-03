<?php

namespace App\Http\Controllers;

use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Models\WarehouseModel;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    public function index(): JsonResponse
    {
        $items = WarehouseModel::with([
            'receiving',
            'supplier',
            'type',
            'category',
            'model',
            'unit',
            'location',
        ])->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }

    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        $warehouse = WarehouseModel::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Warehouse item created successfully.',
            'data'    => $warehouse->load([
                'receiving', 'supplier', 'type',
                'category', 'model', 'unit', 'location',
            ]),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $warehouse = WarehouseModel::with([
            'receiving',
            'supplier',
            'type',
            'category',
            'model',
            'unit',
            'location',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $warehouse,
        ]);
    }

    public function update(UpdateWarehouseRequest $request, int $id): JsonResponse
    {
        $warehouse = WarehouseModel::findOrFail($id);
        $warehouse->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Warehouse item updated successfully.',
            'data'    => $warehouse->load([
                'receiving', 'supplier', 'type',
                'category', 'model', 'unit', 'location',
            ]),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $warehouse = WarehouseModel::findOrFail($id);
        $warehouse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Warehouse item deleted successfully.',
        ]);
    }
}
