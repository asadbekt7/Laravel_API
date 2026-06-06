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
        $query = InformationModel::with(['supplier', 'location'])
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

        if ($request->hasFile('shartnoma_fayl')) {
            $validated['shartnoma_fayl'] = $request->file('shartnoma_fayl')
                ->store('shartnomalar', 'public');
        }

        if ($request->hasFile('ishonchnoma_fayl')) {
            $validated['ishonchnoma_fayl'] = $request->file('ishonchnoma_fayl')
                ->store('ishonchnomalar', 'public');
        }

        $information = InformationModel::create($validated);
        $information->load(['supplier', 'location']);

        return response()->json([
            'message' => 'Muvaffaqiyatli yaratildi',
            'data'    => $information,
        ], 201);
    }

    // GET /api/informations/{id}
    public function show(int $id): JsonResponse
    {
        $information = InformationModel::with(['supplier', 'location'])
            ->findOrFail($id);

        return response()->json(['data' => $information]);
    }

    // PUT /api/informations/{id}
    public function update(UpdateInformationRequest $request, int $id): JsonResponse
    {
        $information = InformationModel::findOrFail($id);
        $validated   = $request->validated();

        if ($request->hasFile('shartnoma_fayl')) {
            Storage::disk('public')->delete($information->shartnoma_fayl ?? '');
            $validated['shartnoma_fayl'] = $request->file('shartnoma_fayl')
                ->store('shartnomalar', 'public');
        }

        if ($request->hasFile('ishonchnoma_fayl')) {
            Storage::disk('public')->delete($information->ishonchnoma_fayl ?? '');
            $validated['ishonchnoma_fayl'] = $request->file('ishonchnoma_fayl')
                ->store('ishonchnomalar', 'public');
        }

        $information->update($validated);
        $information->load(['supplier', 'location']);

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
            $information->shartnoma_fayl,
            $information->ishonchnoma_fayl,
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
