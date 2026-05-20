<?php

namespace App\Http\Controllers;

use App\Http\Requests\Model\StoreModelRequest;
use App\Http\Requests\Model\UpdateModelRequest;
use App\Models\GoodModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $models = GoodModel::with('category:id,name')
            ->when($request->search, fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->orderBy('id')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $models
        ]);
    }

    public function store(StoreModelRequest $request): JsonResponse
    {
        $model = GoodModel::create($request->validated());
        $model->load('category:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Model has been created',
            'data'    => $model
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $model = GoodModel::with('category:id,name')->find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, model with id {$id} cannot be found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $model
        ]);
    }

    public function update(UpdateModelRequest $request, int $id): JsonResponse
    {
        $model = GoodModel::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model topilmadi'
            ], 404);
        }

        $model->update($request->validated());
        $model->load('category:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Model tahrirlandi',
            'data'    => $model
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $model = GoodModel::find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Model topilmadi'
            ], 404);
        }

        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Model muvaffaqiyatli ochirildi'
        ]);
    }
}
