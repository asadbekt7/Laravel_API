<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseStoreRequest;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    public function __construct(
        private readonly WarehouseService $warehouseService
    ) {}

    public function index(): JsonResponse
    {
        $items = $this->warehouseService->getAll();

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }

    public function store(WarehouseStoreRequest $request): JsonResponse
    {
        $records = $this->warehouseService->createBulk($request->validated());

        return response()->json([
            'success' => true,
            'message' => count($records) . ' ta record muvaffaqiyatli yaratildi',
            'count'   => count($records),
            'data'    => $records,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->warehouseService->findById($id);

        return response()->json([
            'success' => true,
            'data'    => $item,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->warehouseService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Record o\'chirildi',
        ]);
    }
}
