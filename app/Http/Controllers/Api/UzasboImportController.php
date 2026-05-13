<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UzasboImportModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UzasboImportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = UzasboImportModel::query()
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $data->items(),
            'meta'    => [
                'total'        => $data->total(),
                'per_page'     => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => UzasboImportModel::findOrFail($id),
        ]);
    }
}
