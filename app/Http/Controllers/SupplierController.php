<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = SupplierModel::all();

        return response()->json([
            'success' => true,
            'data'    => $suppliers,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier topilmadi',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $supplier,
        ]);
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = SupplierModel::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Supplier muvaffaqiyatli yaratildi',
            'data'    => $supplier,
        ], 201);
    }

    public function update(UpdateSupplierRequest $request, int $id): JsonResponse
    {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier topilmadi',
            ], 404);
        }

        $supplier->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Supplier muvaffaqiyatli yangilandi',
            'data'    => $supplier,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier topilmadi',
            ], 404);
        }

        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier muvaffaqiyatli o\'chirildi',
        ]);
    }
}
