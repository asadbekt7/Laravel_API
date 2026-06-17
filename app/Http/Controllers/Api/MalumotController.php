<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\MalumotFilter;
use App\Http\Requests\Malumot\StoreMalumotRequest;
use App\Http\Requests\Malumot\UpdateMalumotRequest;
use App\Http\Resources\Malumot\MalumotResource;
use App\Models\MalumotModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class MalumotController extends Controller
{
    public function index(MalumotFilter $filter): AnonymousResourceCollection
    {
        $malumotlar = MalumotModel::with(['yetkazuvchi', 'unit'])
            ->pipe(fn ($query) => $filter->apply($query))
            ->tap(fn ($query) => $filter->applySorting($query, request()))
            ->paginate($filter->getPerPage(request()));

        return MalumotResource::collection($malumotlar);
    }

    public function store(StoreMalumotRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Fayllarni saqlash
        $data['contract_file_path']       = $this->uploadFile($request, 'contract_file_path', 'contracts');
        $data['bildirishnoma_file_path']  = $this->uploadFile($request, 'bildirishnoma_file_path', 'bildirishnomalar');
        $data['ishonchnoma_file_path']    = $this->uploadFile($request, 'ishonchnoma_file_path', 'ishonchnomalar');
        $data['hisob_faktura_file_path']  = $this->uploadFile($request, 'hisob_faktura_file_path', 'hisob_fakturalar');

        $malumot = MalumotModel::create($data);
        $malumot->load(['yetkazuvchi', 'unit']);

        return response()->json([
            'message' => 'Malumot muvaffaqiyatli yaratildi.',
            'data'    => new MalumotResource($malumot),
        ], 201);
    }

    public function show(MalumotModel $malumot): JsonResponse
    {
        $malumot->load(['yetkazuvchi', 'unit']);

        return response()->json([
            'data' => new MalumotResource($malumot),
        ]);
    }

    public function update(UpdateMalumotRequest $request, MalumotModel $malumot): JsonResponse
    {
        $data = $request->validated();

        // Yangi fayllar yuklansa — eskisini o'chir va yangisini saqlash
        if ($request->hasFile('contract_file_path')) {
            $this->deleteFile($malumot->contract_file_path);
            $data['contract_file_path'] = $this->uploadFile($request, 'contract_file_path', 'contracts');
        }

        if ($request->hasFile('bildirishnoma_file_path')) {
            $this->deleteFile($malumot->bildirishnoma_file_path);
            $data['bildirishnoma_file_path'] = $this->uploadFile($request, 'bildirishnoma_file_path', 'bildirishnomalar');
        }

        if ($request->hasFile('ishonchnoma_file_path')) {
            $this->deleteFile($malumot->ishonchnoma_file_path);
            $data['ishonchnoma_file_path'] = $this->uploadFile($request, 'ishonchnoma_file_path', 'ishonchnomalar');
        }

        if ($request->hasFile('hisob_faktura_file_path')) {
            $this->deleteFile($malumot->hisob_faktura_file_path);
            $data['hisob_faktura_file_path'] = $this->uploadFile($request, 'hisob_faktura_file_path', 'hisob_fakturalar');
        }

        $malumot->update($data);
        $malumot->load(['yetkazuvchi', 'unit']);

        return response()->json([
            'message' => 'Malumot muvaffaqiyatli yangilandi.',
            'data'    => new MalumotResource($malumot),
        ]);
    }

    public function destroy(MalumotModel $malumot): JsonResponse
    {
        // Barcha fayllarni diskdan o'chirish
        $this->deleteFile($malumot->contract_file_path);
        $this->deleteFile($malumot->bildirishnoma_file_path);
        $this->deleteFile($malumot->ishonchnoma_file_path);
        $this->deleteFile($malumot->hisob_faktura_file_path);

        $malumot->delete();

        return response()->json([
            'message' => 'Malumot muvaffaqiyatli o\'chirildi.',
        ]);
    }

    private function uploadFile($request, string $field, string $folder): string
    {
        return $request->file($field)->store($folder, 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
