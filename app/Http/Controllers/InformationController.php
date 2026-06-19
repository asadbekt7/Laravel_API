<?php

namespace App\Http\Controllers;

use App\Http\Filters\InformationFilter;
use App\Http\Requests\Information\StoreInformationRequest;
use App\Http\Requests\Information\UpdateInformationRequest;
use App\Models\InformationModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class InformationController extends Controller
{
    // GET /api/informations
    public function index(InformationFilter $filter): JsonResponse
    {
        $query = InformationModel::with(['supplier', 'unit'])
            ->filter($filter);

        $filter->applySorting($query, request());

        $data = $query->paginate($filter->getPerPage(request()))
            ->appends(request()->query());

        return response()->json($data);
    }

    // POST /api/informations
    public function store(StoreInformationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('contract_file_path')) {
            $validated['contract_file_path'] = $request->file('contract_file_path')
                ->store('contracts', 'public');
        }

        if ($request->hasFile('bildirishnoma_file_path')) {
            $validated['bildirishnoma_file_path'] = $request->file('bildirishnoma_file_path')
                ->store('bildirishnomalar', 'public');
        }

        if ($request->hasFile('ishonchnoma_file_path')) {
            $validated['ishonchnoma_file_path'] = $request->file('ishonchnoma_file_path')
                ->store('ishonchnomalar', 'public');
        }

        if ($request->hasFile('hisob_faktura_file_path')) {
            $validated['hisob_faktura_file_path'] = $request->file('hisob_faktura_file_path')
                ->store('hisob_fakturalar', 'public');
        }

        $information = InformationModel::create($validated);
        $information->load(['supplier', 'unit']);

        return response()->json([
            'message' => 'Muvaffaqiyatli yaratildi',
            'data'    => $information,
        ], 201);
    }

    // GET /api/informations/{id}
    public function show(int $id): JsonResponse
    {
        $information = InformationModel::with(['supplier', 'unit'])
            ->findOrFail($id);

        return response()->json(['data' => $information]);
    }

    // PUT /api/informations/{id}
    public function update(UpdateInformationRequest $request, int $id): JsonResponse
    {
        $information = InformationModel::findOrFail($id);
        $validated   = $request->validated();

        if ($request->hasFile('contract_file_path')) {
            Storage::disk('public')->delete($information->contract_file_path ?? '');
            $validated['contract_file_path'] = $request->file('contract_file_path')
                ->store('contracts', 'public');
        }

        if ($request->hasFile('bildirishnoma_file_path')) {
            Storage::disk('public')->delete($information->bildirishnoma_file_path ?? '');
            $validated['bildirishnoma_file_path'] = $request->file('bildirishnoma_file_path')
                ->store('bildirishnomalar', 'public');
        }

        if ($request->hasFile('ishonchnoma_file_path')) {
            Storage::disk('public')->delete($information->ishonchnoma_file_path ?? '');
            $validated['ishonchnoma_file_path'] = $request->file('ishonchnoma_file_path')
                ->store('ishonchnomalar', 'public');
        }

        if ($request->hasFile('hisob_faktura_file_path')) {
            Storage::disk('public')->delete($information->hisob_faktura_file_path ?? '');
            $validated['hisob_faktura_file_path'] = $request->file('hisob_faktura_file_path')
                ->store('hisob_fakturalar', 'public');
        }

        $information->update($validated);
        $information->load(['supplier', 'unit']);

        return response()->json([
            'message' => 'Muvaffaqiyatli yangilandi',
            'data'    => $information,
        ]);
    }

    // DELETE /api/informations/{id}
    public function destroy(int $id): JsonResponse
    {
        InformationModel::findOrFail($id)->delete();

        return response()->json(['message' => "Muvaffaqiyatli o'chirildi"]);
    }

    // DELETE /api/informations/{id}/force
    public function forceDestroy(int $id): JsonResponse
    {
        $information = InformationModel::withTrashed()->findOrFail($id);

        Storage::disk('public')->delete(array_filter([
            $information->contract_file_path,
            $information->bildirishnoma_file_path,
            $information->ishonchnoma_file_path,
            $information->hisob_faktura_file_path,
        ]));

        $information->forceDelete();

        return response()->json(['message' => "Butunlay o'chirildi"]);
    }

    // POST /api/informations/{id}/restore
    public function restore(int $id): JsonResponse
    {
        $information = InformationModel::withTrashed()->findOrFail($id);
        $information->restore();

        return response()->json([
            'message' => 'Muvaffaqiyatli tiklandi',
            'data'    => $information,
        ]);
    }
}
