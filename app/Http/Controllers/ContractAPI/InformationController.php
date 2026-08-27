<?php

declare(strict_types=1);

namespace App\Http\Controllers\ContractAPI;

use App\DTOs\InformationItemData;
use App\Enums\InformationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Information\StoreInformationRequest;
use App\Http\Requests\Information\UpdateInformationRequest;
use App\Http\Resources\Information\InformationResource;
use App\Models\InformationModel;
use App\Services\InformationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class InformationController extends Controller
{
    public function __construct(private readonly InformationService $service)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'supplier_id', 'from_date', 'to_date', 'status', 'created_by_me']);
        $perPage = min($request->integer('per_page', 15), 100);

        return InformationResource::collection($this->service->paginate($filters, $perPage));
    }

    public function pending(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 15), 100);
        $filters = ['status' => InformationStatus::Pending->value];

        return InformationResource::collection($this->service->paginate($filters, $perPage));
    }

    public function show(InformationModel $information): InformationResource
    {
        return new InformationResource(
            $information->load('supplier', 'creator', 'items.unit')
        );
    }

    public function store(StoreInformationRequest $request): JsonResponse
    {
        $items = array_map(
            fn (array $item) => InformationItemData::fromArray($item),
            $request->validated('items')
        );

        try {
            $information = $this->service->create($request->validated(), $items);

            return response()->json([
                'message' => 'Muvaffaqiyatli saqlandi.',
                'data'    => new InformationResource($information),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Information store xatosi', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Saqlashda xatolik yuz berdi.'], 500);
        }
    }

    public function update(UpdateInformationRequest $request, InformationModel $information): JsonResponse
    {
        $items = $request->has('items')
            ? array_map(
                fn (array $item) => InformationItemData::fromArray($item, isset($item['id']) ? (int) $item['id'] : null),
                $request->validated('items')
            )
            : null;

        try {
            $updated = $this->service->update($information, $request->validated(), $items);

            return response()->json([
                'message' => 'Muvaffaqiyatli yangilandi.',
                'data'    => new InformationResource($updated),
            ]);
        } catch (\Throwable $e) {
            Log::error('Information update xatosi', ['error' => $e->getMessage(), 'id' => $information->id]);

            return response()->json(['message' => 'Yangilashda xatolik yuz berdi.'], 500);
        }
    }

    public function destroy(InformationModel $information): JsonResponse
    {
        $this->authorize('delete', $information);

        $this->service->delete($information);

        return response()->json(['message' => "Muvaffaqiyatli o'chirildi."]);
    }

    public function accept(InformationModel $information): JsonResponse
    {
        if (! $this->service->accept($information)) {
            return response()->json([
                'message' => "Bu ma'lumotni qabul qilib bo'lmaydi. U allaqachon qabul qilingan yoki yakunlangan.",
            ], 422);
        }

        return response()->json([
            'message' => 'Muvaffaqiyatli qabul qilindi.',
            'data'    => new InformationResource($information->fresh(['supplier', 'creator', 'items.unit'])),
        ]);
    }

    public function start(InformationModel $information): JsonResponse
    {
        if (! $this->service->start($information)) {
            return response()->json([
                'message' => "Ishni boshlab bo'lmaydi. Avval ma'lumot qabul qilingan bo'lishi kerak.",
            ], 422);
        }

        return response()->json([
            'message' => 'Ish boshlandi.',
            'data'    => new InformationResource($information->fresh(['supplier', 'creator', 'items.unit'])),
        ]);
    }

    public function complete(InformationModel $information): JsonResponse
    {
        if (! $this->service->complete($information)) {
            return response()->json([
                'message' => "Ishni yakunlab bo'lmaydi. Ma'lumot jarayonda bo'lishi kerak.",
            ], 422);
        }

        return response()->json([
            'message' => 'Ish muvaffaqiyatli yakunlandi.',
            'data'    => new InformationResource($information->fresh(['supplier', 'creator', 'items.unit'])),
        ]);
    }
}
