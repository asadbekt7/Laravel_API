<?php

namespace App\Http\Controllers\Api;

use App\Enums\ItemType;
use App\Http\Controllers\Controller;
use App\Http\Filters\WarehouseFilter;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Models\CategoryModel;
use App\Models\GoodModel;
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

    public function itemTypes(): JsonResponse
    {
        return response()->json(['data' => ItemType::options()]);
    }

    public function statsCategories(Request $request): JsonResponse
    {
        $typeId = (int) ($request->query('type_id') ?: 2);

        $type = TypeModel::find($typeId);
        if (! $type) {
            return response()->json(['message' => 'Type topilmadi'], 404);
        }

        $categories = CategoryModel::where('type_id', $typeId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $modelCounts = GoodModel::whereIn('category_id', $categories->pluck('id'))
            ->select('category_id', DB::raw('COUNT(*) as c'))
            ->groupBy('category_id')
            ->pluck('c', 'category_id');

        $sums = WarehouseModel::query()
            ->leftJoin('models', 'models.id', '=', 'warehouse.model_id')
            ->where('warehouse.type_id', $typeId)
            ->select(
                DB::raw('COALESCE(warehouse.category_id, models.category_id) as cat_id'),
                DB::raw('SUM(warehouse.quantity) as q')
            )
            ->groupBy(DB::raw('COALESCE(warehouse.category_id, models.category_id)'))
            ->pluck('q', 'cat_id');

        $out = $categories->map(fn ($c) => [
            'id'             => (int) $c->id,
            'name'           => $c->name,
            'model_count'    => (int) ($modelCounts[$c->id] ?? 0),
            'total_quantity' => (int) ($sums[$c->id] ?? 0),
        ])->values();

        return response()->json([
            'type'           => ['id' => $type->id, 'name' => $type->name],
            'category_count' => $out->count(),
            'total_quantity' => (int) $out->sum('total_quantity'),
            'categories'     => $out,
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

        $models = GoodModel::where('category_id', $categoryId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $sums = WarehouseModel::whereIn('model_id', $models->pluck('id'))
            ->select('model_id', DB::raw('SUM(quantity) as q'))
            ->groupBy('model_id')
            ->pluck('q', 'model_id');

        $out = $models->map(fn ($m) => [
            'id'             => (int) $m->id,
            'name'           => $m->name,
            'total_quantity' => (int) ($sums[$m->id] ?? 0),
        ])->values();

        return response()->json([
            'type'           => $category->type
                ? ['id' => $category->type->id, 'name' => $category->type->name]
                : null,
            'category'       => ['id' => $category->id, 'name' => $category->name],
            'model_count'    => $out->count(),
            'total_quantity' => (int) $out->sum('total_quantity'),
            'models'         => $out,
        ]);
    }
}
