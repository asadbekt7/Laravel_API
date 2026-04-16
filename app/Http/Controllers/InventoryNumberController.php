<?php

namespace App\Http\Controllers;

use App\Models\Inventorynumbermodel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InventoryNumberController extends Controller
{
    public function index()
    {
        $inventorynumber = Inventorynumbermodel::all();
        return response()->json([
            'success' => true,
            'data' => $inventorynumber
        ]);
    }

    public function show($id)
    {
        $inventorynumber = Inventorynumbermodel::find($id);
        if (!$inventorynumber) {
            return response()->json([
                'success' => false,
                'message' => 'inventory number with id ' . $id . ' not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $inventorynumber
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'inventory_number' => 'required|integer|unique:inventory_number,inventory_number',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);
        }

        $inventorynumber = Inventorynumbermodel::create([
            'inventory_number' => $request->inventory_number,
        ]);

        return response()->json([
            'success' => true,
            'data' => $inventorynumber
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $inventorynumber = Inventorynumbermodel::find($id);
        if (!$inventorynumber) {
            return response()->json([
                'success' => false,
                'message' => 'inventory number with id ' . $id . ' not found'
            ], 404);
        }

        try {
            $request->validate([
                'inventory_number' => 'required|integer|unique:inventory_number,inventory_number,' . $id,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);
        }

        $inventorynumber->update([
            'inventory_number' => $request->inventory_number,
        ]);

        return response()->json([
            'success' => true,
            'data' => $inventorynumber
        ]);
    }
    public function destroy($id)
    {
        $inventorynumber = Inventorynumbermodel::find($id);

        if (!$inventorynumber) {
            return response()->json([
                'success' => false,
                'message' => 'inventory number with id ' . $id . ' not found'
            ], 404);
        }

        $inventorynumber->delete();

        return response()->json([
            'success' => true,
            'message' => 'inventory number deleted successfully'
        ]);
    }
}
