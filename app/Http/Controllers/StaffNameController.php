<?php

namespace App\Http\Controllers;

use App\Models\ItemsModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffNameController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $allowedSorts = ['full_name', 'department', 'total_items'];
        $sort = in_array($request->sort, $allowedSorts, true) ? $request->sort : 'full_name';
        $dir  = strtolower((string) $request->dir) === 'desc' ? 'desc' : 'asc';

        $staffNames = ItemsModel::query()
            ->select('full_name', 'first_name', 'last_name', 'middle_name', 'department')
            ->selectRaw('COUNT(*) as total_items')
            ->when($request->search, function ($q) use ($request) {
                $term = '%' . strtolower($request->search) . '%';
                $q->where(function ($w) use ($term) {
                    $w->whereRaw('LOWER(full_name) like ?', [$term])
                        ->orWhereRaw('LOWER(department) like ?', [$term]);
                });
            })
            ->groupBy('full_name', 'first_name', 'last_name', 'middle_name', 'department')
            ->orderBy($sort, $dir)
            ->paginate($request->integer('per_page', 25));

        return response()->json([
            'success' => true,
            'data'    => $staffNames,
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
