<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Type\StoreTypeRequest;
use App\Http\Requests\Type\UpdateTypeRequest;
use App\Models\TypeModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $types = TypeModel::query()
            ->when($request->search, fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->orderBy('id')
            ->paginate($request->integer('per_page', 15));

        return response()->json($types);
    }

    public function store(StoreTypeRequest $request): JsonResponse
    {
        $type = TypeModel::create($request->validated());

        return response()->json($type, 201);
    }

    public function show(int $id): JsonResponse
    {
        $type = TypeModel::findOrFail($id);

        return response()->json($type);
    }

    public function update(UpdateTypeRequest $request, int $id): JsonResponse
    {
        $type = TypeModel::findOrFail($id);
        $type->update($request->validated());

        return response()->json($type);
    }

    public function destroy(int $id): JsonResponse
    {
        TypeModel::findOrFail($id)->delete();

        return response()->json(['message' => 'Type deleted successfully']);
    }
}
