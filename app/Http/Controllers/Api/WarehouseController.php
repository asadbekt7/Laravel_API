<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\WarehouseFilter;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Models\InformationModel;
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
        $data        = $request->validated();
        $information = InformationModel::findOrFail($data['information_id']);

        // item_type / expiry_date / statya mantiqi OLIB TASHLANDI —
        // bu maydonlar warehouse'da yo'q, buxgalter keyin to'ldiradi.

        $warehouse = WarehouseModel::create([
            ...$data,
            // 4 ta maydon Information'dan avtomatik olinadi —
            // frontend yuborgan qiymatlar emas
            'name'          => $information->product_name,
            'quantity'      => $information->quantity,
            'unit_id'       => $information->unit_id,
            'product_price' => $information->price,
            'assignee_id'   => auth()->id(), // kiritayotgan odam avtomatik
        ]);
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
}
