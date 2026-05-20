<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\CategoryModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = CategoryModel::select('id', 'name', 'type_id')
            ->with('type:id,name')
            ->when($request->search, fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->when($request->type_id, fn($q) => $q->where('type_id', $request->type_id))
            ->orderBy('id')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $categories
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = CategoryModel::create($request->validated());
        $category->load('type:id,name');

        return response()->json([
            'success' => true,
            'data'    => $category
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = CategoryModel::with('type:id,name')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, Category with id {$id} cannot be found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $category
        ]);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = CategoryModel::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, Category with id {$id} cannot be found"
            ], 404);
        }

        $category->update($request->validated());
        $category->load('type:id,name');

        return response()->json([
            'success' => true,
            'data'    => $category
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = CategoryModel::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, Category with id {$id} cannot be found"
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
