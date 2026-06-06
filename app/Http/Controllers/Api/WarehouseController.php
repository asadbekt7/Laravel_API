<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\WarehouseFilter;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Models\WarehouseModel;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    private array $relations = [
        'information',
        'type',
        'category',
        'model',
        'unit',
        'location',
    ];

    // GET /api/warehouse
    public function index(WarehouseFilter $filter): JsonResponse
    {
        $query = WarehouseModel::with($this->relations)
            ->filter($filter);

        $filter->applySorting($query, $filter->request);

        $data = $query->paginate($filter->getPerPage($filter->request))
            ->appends($filter->request->query());

        return response()->json($data);
    }

    // POST /api/warehouse
    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        $warehouse = WarehouseModel::create($request->validated());
        $warehouse->load($this->relations);

        return response()->json([
            'message' => 'Muvaffaqiyatli yaratildi',
            'data'    => $warehouse,
        ], 201);
    }

    // GET /api/warehouse/{id}
    public function show(int $id): JsonResponse
    {
        $warehouse = WarehouseModel::with($this->relations)->findOrFail($id);

        return response()->json(['data' => $warehouse]);
    }

    // PUT /api/warehouse/{id}
    public function update(UpdateWarehouseRequest $request, int $id): JsonResponse
    {
        $warehouse = WarehouseModel::findOrFail($id);
        $warehouse->update($request->validated());
        $warehouse->load($this->relations);

        return response()->json([
            'message' => 'Muvaffaqiyatli yangilandi',
            'data'    => $warehouse,
        ]);
    }

    // DELETE /api/warehouse/{id}
    public function destroy(int $id): JsonResponse
    {
        WarehouseModel::findOrFail($id)->delete();

        return response()->json(['message' => "Muvaffaqiyatli o'chirildi"]);
    }
}
