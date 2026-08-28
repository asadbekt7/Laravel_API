<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Receiving\GenerateReceivingActPdfAction;
use App\DTOs\Receiving\ReceivingActData;
use App\DTOs\Warehouse\WarehouseAcceptData;
use App\DTOs\Warehouse\WarehouseItemClassificationData;
use App\DTOs\Warehouse\WarehouseUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\AcceptWarehouseRequest;
use App\Http\Requests\Warehouse\RejectWarehouseRequest;
use App\Http\Resources\Information\InformationResource;
use App\Http\Resources\Warehouse\WarehouseResource;
use App\Http\Requests\Warehouse\WarehouseIndexRequest;
use App\Http\Resources\Warehouse\WarehouseListResource;
use App\Http\Requests\Warehouse\WarehouseUpdateRequest;
use App\Models\InformationModel;
use App\Models\WarehouseModel;
use App\Services\InformationService;
use App\Services\WarehouseAcceptanceService;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseController extends Controller
{
    public function __construct(
        private readonly WarehouseAcceptanceService $acceptanceService,
        private readonly InformationService $informationService,
        private readonly WarehouseService $warehouseService,
    ) {
    }

    /**
     * Ombor mudiri ko'rishi kerak bo'lgan, hali qaror qabul qilinmagan
     * information'lar ro'yxati.
     *
     * GET /api/warehouse/pending-informations
     */
    public function pendingInformations(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->integer('per_page', 15), 100);

        $informations = $this->informationService->paginate(['status' => 'pending'], $perPage);

        return InformationResource::collection($informations);
    }

    /**
     * POST /api/warehouse/{information}/accept
     *
     * Qabul qilishda warehouse.status har doim DEFAULT ACCEPTED bo'ladi.
     */
    public function accept(AcceptWarehouseRequest $request, InformationModel $information): JsonResponse
    {
        $items = array_map(
            fn (array $item) => WarehouseItemClassificationData::fromArray($item),
            $request->validated('items')
        );

        $data = new WarehouseAcceptData(
            aktNumber: $request->validated('akt_number'),
            aktDate: $request->validated('akt_date'),
            locationId: (int) $request->validated('location_id'),
            description: $request->validated('description'),
            items: $items,
        );

        try {
            $warehouse = $this->acceptanceService->accept($information, $data);

            return response()->json([
                'message' => 'Omborga muvaffaqiyatli qabul qilindi.',
                'data'    => new WarehouseResource($warehouse),
            ], 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /api/warehouse/{information}/reject
     *
     * DIQQAT: reject holatida `warehouse` jadvaliga hech narsa yozilmaydi -
     * faqat information.status = Rejected va reject_reason to'ldiriladi.
     */
    public function reject(RejectWarehouseRequest $request, InformationModel $information): JsonResponse
    {
        $rejected = $this->acceptanceService->reject($information, $request->validated('reject_reason'));

        if (! $rejected) {
            return response()->json([
                'message' => "Bu ma'lumotni rad etib bo'lmaydi - u allaqachon ko'rib chiqilgan.",
            ], 422);
        }

        return response()->json([
            'message' => 'Rad etildi.',
            'data'    => new InformationResource($information->fresh()),
        ]);
    }
    /**
     * Qabul qilingan aktlar ro'yxati.
     *
     * GET /api/receiving
     */
    public function index(WarehouseIndexRequest $request): AnonymousResourceCollection
    {
        $perPage = min((int) $request->validated('per_page', 15), 100);

        $warehouses = $this->warehouseService->paginate($request->validated(), $perPage);

        return WarehouseListResource::collection($warehouses);
    }

    /**
     * Bitta aktning to'liq ma'lumotlari (akt_number, akt_date + tovar qatorlari).
     *
     * GET /api/receiving/{warehouse}
     */
    public function show(WarehouseModel $warehouse): WarehouseResource
    {
        return new WarehouseResource($this->warehouseService->detail($warehouse));
    }
    /**
     * Akt raqami/sanasini tahrirlash.
     *
     * PUT /api/warehouse/receiving/{warehouse}
     */
    public function update(WarehouseUpdateRequest $request, WarehouseModel $warehouse): WarehouseResource
    {
        $data = new WarehouseUpdateData(
            aktNumber: $request->validated('akt_number'),
            aktDate: $request->validated('akt_date'),
        );

        $updated = $this->acceptanceService->updateAkt($warehouse, $data);

        return new WarehouseResource($updated);
    }
}
