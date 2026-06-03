<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemsModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    /**
     * GET /api/items
     */
    public function index(Request $request): JsonResponse
    {
        $query = ItemsModel::with(['unit', 'type', 'category', 'model', 'uzasboImport']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('building')) {
            $query->where('building', $request->building);
        }

        if ($request->filled('room_number')) {
            $query->where('room_number', $request->room_number);
        }
        if ($request->filled('full_name')) {
            $query->where('full_name', 'like', '%' . $request->full_name . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('inventory_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $items   = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }

    /**
     * GET /api/items/{id}
     */
    public function show(int $id): JsonResponse
    {
        $item = ItemsModel::with(['unit', 'type', 'category', 'model', 'uzasboImport'])
            ->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item topilmadi.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $item,
        ]);
    }
}
