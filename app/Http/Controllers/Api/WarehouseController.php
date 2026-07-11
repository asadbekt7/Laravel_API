<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\WarehouseFilter;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Http\Requests\Warehouse\BulkStoreWarehouseRequest;
use App\Models\WarehouseModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
        $warehouse = WarehouseModel::create([
            ...$request->validated(),
            'assignee_id' => auth()->id(), // qabul qilgan (kiritayotgan) odam avtomatik
        ]);
        $warehouse->load($this->relations);

        return response()->json([
            'message' => 'Muvaffaqiyatli yaratildi',
            'data'    => $warehouse,
        ], 201);
    }

    // POST /api/warehouse/bulk
    public function bulkStore(BulkStoreWarehouseRequest $request): JsonResponse
    {
        $informationId = $request->validated()['information_id'];
        $items         = $request->validated()['items'];

        $created = DB::transaction(function () use ($informationId, $items) {
            return collect($items)->map(function (array $item) use ($informationId) {
                $warehouse = WarehouseModel::create([
                    'information_id' => $informationId,
                    'assignee_id'    => auth()->id(), // avtomatik
                    ...$item,
                ]);
                return $warehouse->load($this->relations);
            });
        });

        return response()->json([
            'message' => "{$created->count()} ta mahsulot muvaffaqiyatli yaratildi",
            'data'    => $created,
        ], 201);
    }

    // GET /api/warehouse/{id}
    public function show(int $id): JsonResponse
    {
        $warehouse = WarehouseModel::with($this->relations)->findOrFail($id);

        return response()->json(['data' => $warehouse]);
    }
}
