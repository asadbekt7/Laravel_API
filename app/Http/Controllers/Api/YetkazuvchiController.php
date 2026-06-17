<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Yetkazuvchi\StoreYetkazuvchiRequest;
use App\Http\Requests\Yetkazuvchi\UpdateYetkazuvchiRequest;
use App\Http\Resources\Yetkazuvchi\YetkazuvchiResource;
use App\Models\YetkazuvchiModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class YetkazuvchiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $yetkazuvchilar = YetkazuvchiModel::latest()->paginate(15);

        return YetkazuvchiResource::collection($yetkazuvchilar);
    }

    public function store(StoreYetkazuvchiRequest $request): JsonResponse
    {
        $yetkazuvchi = YetkazuvchiModel::create($request->validated());

        return response()->json([
            'message' => 'Yetkazuvchi muvaffaqiyatli yaratildi.',
            'data'    => new YetkazuvchiResource($yetkazuvchi),
        ], 201);
    }
    public function show(YetkazuvchiModel $yetkazuvchi): JsonResponse
    {
        return response()->json([
            'data' => new YetkazuvchiResource($yetkazuvchi),
        ]);
    }
    public function update(UpdateYetkazuvchiRequest $request, YetkazuvchiModel $yetkazuvchi): JsonResponse
    {
        $yetkazuvchi->update($request->validated());

        return response()->json([
            'message' => 'Yetkazuvchi muvaffaqiyatli yangilandi.',
            'data'    => new YetkazuvchiResource($yetkazuvchi),
        ]);
    }
    public function destroy(YetkazuvchiModel $yetkazuvchi): JsonResponse
    {
        $yetkazuvchi->delete();

        return response()->json([
            'message' => 'Yetkazuvchi muvaffaqiyatli o\'chirildi.',
        ]);
    }
}
