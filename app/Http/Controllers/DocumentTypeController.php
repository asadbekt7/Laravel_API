<?php

namespace App\Http\Controllers;

use App\Models\DocumentTypeModel;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $document_types = DocumentTypeModel::select ('id', 'name')->get();
        return response()->json([
            'success' => true,
            'data' => $document_types
        ]);

    }
    public function show(string $id)
    {
        $document_type = DocumentTypeModel::find($id);
        if (!$document_type){
            return response()->json([
                'success' => false,
                'massage' => 'document_type with id ' . $id . ' cannot be found'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $document_type
        ]);
    }
}
