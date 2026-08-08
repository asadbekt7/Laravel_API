<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\WarehouseFilter;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Models\CategoryModel;
use App\Models\InformationModel;
use App\Models\TypeModel;
use App\Models\WarehouseModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function statsCategories(Request $request): JsonResponse
    {
        $typeId = (int) ($request->query('type_id') ?: 2);

        $type = TypeModel::find($typeId);
        if (! $type) {
            return response()->json(['message' => 'Type topilmadi'], 404);
        }

        $rows = WarehouseModel::query()
            ->join('categories', 'categories.id', '=', 'warehouse.category_id')
            ->where('warehouse.type_id', $typeId)
            ->where('warehouse.quantity', '>', 0)
            ->groupBy('warehouse.category_id', 'categories.name')
            ->orderBy('categories.name')
            ->get([
                'warehouse.category_id as id',
                'categories.name as name',
                DB::raw('COUNT(DISTINCT warehouse.model_id) as model_count'),
                DB::raw('SUM(warehouse.quantity) as total_quantity'),
            ]);

        $categories = $rows->map(fn ($r) => [
            'id'             => (int) $r->id,
            'name'           => $r->name,
            'model_count'    => (int) $r->model_count,
            'total_quantity' => (int) $r->total_quantity,
        ])->values();

        return response()->json([
            'type'           => ['id' => $type->id, 'name' => $type->name],
            'category_count' => $categories->count(),
            'total_quantity' => (int) $categories->sum('total_quantity'),
            'categories'     => $categories,
        ]);
    }

    public function statsModels(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer'],
        ]);
        $categoryId = (int) $validated['category_id'];

        $category = CategoryModel::with('type')->find($categoryId);
        if (! $category) {
            return response()->json(['message' => 'Kategoriya topilmadi'], 404);
        }

        $rows = WarehouseModel::query()
            ->join('models', 'models.id', '=', 'warehouse.model_id')
            ->where('warehouse.category_id', $categoryId)
            ->where('warehouse.quantity', '>', 0)
            ->groupBy('warehouse.model_id', 'models.name')
            ->orderBy('models.name')
            ->get([
                'warehouse.model_id as id',
                'models.name as name',
                DB::raw('SUM(warehouse.quantity) as total_quantity'),
            ]);

        $models = $rows->map(fn ($r) => [
            'id'             => (int) $r->id,
            'name'           => $r->name,
            'total_quantity' => (int) $r->total_quantity,
        ])->values();

        return response()->json([
            'type'           => $category->type
                ? ['id' => $category->type->id, 'name' => $category->type->name]
                : null,
            'category'       => ['id' => $category->id, 'name' => $category->name],
            'model_count'    => $models->count(),
            'total_quantity' => (int) $models->sum('total_quantity'),
            'models'         => $models,
        ]);
    }
}
