<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ExternalApi\ApiConnectionException;
use App\Exceptions\ExternalApi\ApiInvalidResponseException;
use App\Exceptions\ExternalApi\ApiRequestFailedException;
use App\Exceptions\InsufficientQuantityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseTransfer\StoreWarehouseTransferRequest;
use App\Services\StaffApiService;
use App\Services\WarehouseTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WarehouseTransferController extends Controller
{
    public function __construct(
        private readonly WarehouseTransferService $transferService,
        private readonly StaffApiService $staffApiService,
    ) {}

    /**
     * GET /api/warehouse-transfer/staff-search?search=Aliyev
     */
    public function staffSearch(Request $request): JsonResponse
    {
        $request->validate([
            'search' => ['required', 'string', 'min:2'],
        ]);

        try {
            $staff = $this->staffApiService->searchStaff($request->string('search'));

            return response()->json(['data' => $staff]);

        } catch (ApiConnectionException|ApiRequestFailedException|ApiInvalidResponseException) {
            return response()->json([
                'message' => 'Xodimlar bazasi bilan bog\'lanishda xatolik. Keyinroq urinib ko\'ring.',
            ], 502);
        }
    }

    /**
     * POST /api/warehouse-transfer
     */
    public function store(StoreWarehouseTransferRequest $request): JsonResponse
    {
        try {
            $items = $this->transferService->handle(
                staff: $request->validated('staff'),
                products: $request->validated('products'),
            );

            return response()->json([
                'message' => "{$items->count()} ta mahsulot \"{$request->validated('staff')['full_name']}\"ga biriktirildi.",
                'data'    => $items,
            ], 201);

        } catch (InsufficientQuantityException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Warehouse transfer xatosi', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Biriktirishda xatolik yuz berdi.',
            ], 500);
        }
    }
}
