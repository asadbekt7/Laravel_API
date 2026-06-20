<?php

namespace App\Http\Controllers;

use App\Http\Requests\Information\BulkInformationRequest;
use App\Http\Requests\Information\StoreInformationRequest;
use App\Http\Requests\Information\UpdateInformationRequest;
use App\Http\Resources\Information\InformationResource;
use App\Models\InformationModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InformationController extends Controller
{
    // ----------------------------------------------------------------
    // Fayl yuklash uchun yordamchi method
    // ----------------------------------------------------------------
    private function uploadFile($file, string $folder): array
    {
        $originalName = $file->getClientOriginalName();
        $path         = $file->store("informations/{$folder}", 'public');

        return [
            'path' => $path,
            'name' => $originalName,
        ];
    }

    // ----------------------------------------------------------------
    // Fayl o'chirish uchun yordamchi method
    // ----------------------------------------------------------------
    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // ----------------------------------------------------------------
    // Validated datadan file path va name larni ajratib olish
    // ----------------------------------------------------------------
    private function resolveFiles(array $validated, Request $request): array
    {
        $fileFields = [
            'contract_file'      => 'contracts',
            'bildirishnoma_file' => 'bildirishnomalar',
            'ishonchnoma_file'   => 'ishonchnomalar',
            'hisob_faktura_file' => 'hisob_fakturalar',
        ];

        foreach ($fileFields as $field => $folder) {
            $pathKey = "{$field}_path";
            $nameKey = "{$field}_name";

            if ($request->hasFile($pathKey)) {
                $uploaded             = $this->uploadFile($request->file($pathKey), $folder);
                $validated[$pathKey]  = $uploaded['path'];
                $validated[$nameKey]  = $uploaded['name'];
            }
        }

        return $validated;
    }

    // ================================================================
    // INDEX - Ro'yxat
    // ================================================================
    public function index(Request $request): AnonymousResourceCollection
    {
        $informations = InformationModel::with('supplier', 'unit')
            ->when($request->search, fn($q) => $q
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('contract_number', 'like', "%{$request->search}%")
                ->orWhere('product_name', 'like', "%{$request->search}%")
            )
            ->when($request->supplier_id, fn($q) => $q
                ->where('supplier_id', $request->supplier_id)
            )
            ->when($request->from_date, fn($q) => $q
                ->whereDate('contract_date', '>=', $request->from_date)
            )
            ->when($request->to_date, fn($q) => $q
                ->whereDate('contract_date', '<=', $request->to_date)
            )
            ->latest()
            ->paginate($request->per_page ?? 15);

        return InformationResource::collection($informations);
    }

    // ================================================================
    // SHOW - Bitta
    // ================================================================
    public function show(InformationModel $information): InformationResource
    {
        return new InformationResource(
            $information->load('supplier', 'unit')
        );
    }

    // ================================================================
    // STORE - Yaratish
    // ================================================================
    public function store(StoreInformationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $this->resolveFiles($request->validated(), $request);

            $information = InformationModel::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Muvaffaqiyatli saqlandi.',
                'data'    => new InformationResource($information->load('supplier', 'unit')),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Xatolik yuz berdi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ================================================================
    // BULK STORE - Ko'p mahsulot bilan yaratish
    // ================================================================
    public function bulkStore(BulkInformationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $this->resolveFiles($request->validated(), $request);

            $items = $validated['items'];
            unset($validated['items']);

            $created = [];
            foreach ($items as $item) {
                $created[] = InformationModel::create(array_merge($validated, $item));
            }

            DB::commit();

            return response()->json([
                'message' => count($created) . ' ta mahsulot muvaffaqiyatli saqlandi.',
                'data'    => InformationResource::collection(
                    collect($created)->load('supplier', 'unit')
                ),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Xatolik yuz berdi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ================================================================
    // UPDATE - Tahrirlash
    // ================================================================
    public function update(UpdateInformationRequest $request, InformationModel $information): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated  = $request->validated();
            $fileFields = [
                'contract_file_path',
                'bildirishnoma_file_path',
                'ishonchnoma_file_path',
                'hisob_faktura_file_path',
            ];

            foreach ($fileFields as $pathKey) {
                $nameKey = str_replace('_path', '_name', $pathKey);
                $folder  = explode('_', $pathKey)[0];

                if ($request->hasFile($pathKey)) {
                    // Eski faylni o'chirish
                    $this->deleteFile($information->$pathKey);

                    // Yangi fayl yuklash
                    $uploaded           = $this->uploadFile($request->file($pathKey), $folder);
                    $validated[$pathKey] = $uploaded['path'];
                    $validated[$nameKey] = $uploaded['name'];
                }
            }

            $information->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Muvaffaqiyatli yangilandi.',
                'data'    => new InformationResource($information->load('supplier', 'unit')),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Xatolik yuz berdi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ================================================================
    // DESTROY - O'chirish (Soft Delete)
    // ================================================================
    public function destroy(InformationModel $information): JsonResponse
    {
        try {
            $information->delete();

            return response()->json([
                'message' => 'Muvaffaqiyatli o\'chirildi.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Xatolik yuz berdi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ================================================================
    // RESTORE - Tiklash (Soft Deletedan)
    // ================================================================
    public function restore(int $id): JsonResponse
    {
        try {
            $information = InformationModel::onlyTrashed()->findOrFail($id);
            $information->restore();

            return response()->json([
                'message' => 'Muvaffaqiyatli tiklandi.',
                'data'    => new InformationResource($information->load('supplier', 'unit')),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Xatolik yuz berdi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ================================================================
    // FORCE DELETE - Butunlay o'chirish (fayllar bilan)
    // ================================================================
    public function forceDelete(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $information = InformationModel::onlyTrashed()->findOrFail($id);

            // Barcha fayllarni o'chirish
            $this->deleteFile($information->contract_file_path);
            $this->deleteFile($information->bildirishnoma_file_path);
            $this->deleteFile($information->ishonchnoma_file_path);
            $this->deleteFile($information->hisob_faktura_file_path);

            $information->forceDelete();

            DB::commit();

            return response()->json([
                'message' => 'Barcha ma\'lumotlar va fayllar butunlay o\'chirildi.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Xatolik yuz berdi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
