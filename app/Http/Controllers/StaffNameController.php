<?php

namespace App\Http\Controllers;

use App\Models\ItemsModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffNameController extends Controller
{
    public function index(): JsonResponse
    {
        $staffNames = ItemsModel::query()
            ->select('full_name', 'first_name', 'last_name', 'middle_name', 'department')
            ->selectRaw('COUNT(*) as total_items')
            ->groupBy('full_name', 'first_name', 'last_name', 'middle_name', 'department')
            ->orderBy('full_name')
            ->get();

        return response()->json([
            'data'  => $staffNames,
            'total' => $staffNames->count(),
        ]);
    }
    public function show(Request $request): JsonResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'min:2'],
        ]);

        $items = ItemsModel::query()
            ->with(['type', 'category', 'model', 'unit'])
            ->where('full_name', $request->full_name)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'full_name'   => $request->full_name,
            'total_items' => $items->count(),
            'data'        => $items,
        ]);
    }
}
