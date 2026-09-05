<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tmz\StoreTmzRequest;
use App\Http\Requests\Tmz\UpdateTmzRequest;
use App\Http\Resources\TmzResource;
use App\Models\Tmz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TmzController extends Controller
{
    /**
     * GET /api/tmz
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);

        $items = Tmz::query()
            ->latest('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => TmzResource::collection($items->items()),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    /**
     * GET /api/tmz/{tmz}
     * Route Model Binding — topilmasa Laravel avtomatik 404 qaytaradi.
     */
    public function show(Tmz $tmz): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new TmzResource($tmz),
        ]);
    }

    /**
     * POST /api/tmz
     */
    public function store(StoreTmzRequest $request): JsonResponse
    {
        $tmz = Tmz::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Muvaffaqiyatli yaratildi.',
            'data' => new TmzResource($tmz),
        ], 201);
    }

    /**
     * PUT/PATCH /api/tmz/{tmz}
     */
    public function update(UpdateTmzRequest $request, Tmz $tmz): JsonResponse
    {
        $tmz->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Muvaffaqiyatli yangilandi.',
            'data' => new TmzResource($tmz->refresh()),
        ]);
    }
}
