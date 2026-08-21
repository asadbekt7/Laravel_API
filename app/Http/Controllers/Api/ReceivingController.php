<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receiving\ReceivingIndexRequest;
use App\Http\Requests\Receiving\UpdateReceivingRequest;
use App\Models\ReceivingModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceivingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $receivings = ReceivingModel::with([
            'documentType:id,name',
            'warehouse'
        ])
            ->when($request->search, fn($q) => $q->where('document_number', 'ilike', "%{$request->search}%")
                ->orWhere('supplier_name', 'ilike', "%{$request->search}%"))
            ->when($request->document_type_id, fn($q) => $q->where('document_type_id', $request->document_type_id))
            ->when($request->from_date, fn($q) => $q->whereDate('document_date', '>=', $request->from_date))
            ->when($request->to_date, fn($q) => $q->whereDate('document_date', '<=', $request->to_date))
            ->orderBy('id', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $receivings
        ]);
    }

    public function store(ReceivingIndexRequest $request): JsonResponse
    {
        $receiving = ReceivingModel::create($request->validated());
        $receiving->load(['documentType:id,name', 'warehouse']);

        return response()->json([
            'success' => true,
            'data'    => $receiving
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $receiving = ReceivingModel::with([
            'documentType:id,name',
            'warehouse'
        ])->find($id);

        if (!$receiving) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, receiving with id {$id} cannot be found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $receiving
        ]);
    }

    public function update(UpdateReceivingRequest $request, int $id): JsonResponse
    {
        $receiving = ReceivingModel::find($id);

        if (!$receiving) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, receiving with id {$id} cannot be found"
            ], 404);
        }

        $receiving->update($request->validated());
        $receiving->load(['documentType:id,name', 'warehouse']);

        return response()->json([
            'success' => true,
            'data'    => $receiving
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $receiving = ReceivingModel::find($id);

        if (!$receiving) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, receiving with id {$id} cannot be found"
            ], 404);
        }

        $receiving->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receiving muvaffaqiyatli ochirildi'
        ]);
    }
}
