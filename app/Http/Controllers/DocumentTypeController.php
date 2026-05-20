<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentType\StoreDocumentTypeRequest;
use App\Http\Requests\DocumentType\UpdateDocumentTypeRequest;
use App\Models\DocumentTypeModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $documentTypes = DocumentTypeModel::query()
            ->when($request->search, fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->orderBy('id')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $documentTypes
        ]);
    }

    public function store(StoreDocumentTypeRequest $request): JsonResponse
    {
        $documentType = DocumentTypeModel::create($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $documentType
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $documentType = DocumentTypeModel::find($id);

        if (!$documentType) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, document type with id {$id} cannot be found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $documentType
        ]);
    }

    public function update(UpdateDocumentTypeRequest $request, int $id): JsonResponse
    {
        $documentType = DocumentTypeModel::find($id);

        if (!$documentType) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, document type with id {$id} cannot be found"
            ], 404);
        }

        $documentType->update($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $documentType
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $documentType = DocumentTypeModel::find($id);

        if (!$documentType) {
            return response()->json([
                'success' => false,
                'message' => "Sorry, document type with id {$id} cannot be found"
            ], 404);
        }

        $documentType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document type muvaffaqiyatli ochirildi'
        ]);
    }
}
