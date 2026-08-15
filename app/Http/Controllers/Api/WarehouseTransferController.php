<?php
// app/Http/Controllers/Api/WarehouseTransferController.php

namespace App\Http\Controllers\Api;

use App\Exceptions\ExternalApi\ApiConnectionException;
use App\Exceptions\ExternalApi\ApiInvalidResponseException;
use App\Exceptions\ExternalApi\ApiRequestFailedException;
use App\Exceptions\InsufficientQuantityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseBatch\StoreWarehouseTransferRequest;
use App\Services\StaffApiService;
use App\Services\WarehouseTransferService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseTransferController extends Controller
{
    public function staffSearch(Request $request, StaffApiService $staffApi): JsonResponse
    {
        $query = trim((string) ($request->input('search') ?? $request->input('q') ?? ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        try {
            $results = $staffApi->searchStaff($query);
        } catch (ApiConnectionException $e) {
            return response()->json(['success' => false, 'message' => 'Xodimlar tizimiga ulanib bo\'lmadi. Qayta urinib ko\'ring.'], 503);
        } catch (ApiRequestFailedException|ApiInvalidResponseException $e) {
            return response()->json(['success' => false, 'message' => 'Xodimlar tizimi xato javob qaytardi.'], 502);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Server xatoligi yuz berdi.'], 500);
        }

        $items = array_map(fn (array $r) => [
            'id'          => $r['id']          ?? null,
            'last_name'   => $r['last_name']   ?? '',
            'first_name'  => $r['first_name']  ?? '',
            'middle_name' => $r['middle_name'] ?? null,
            'full_name'   => $r['full_name']   ?? '',
            'department'  => $r['department']  ?? null,
        ], $results);

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(StoreWarehouseTransferRequest $request, WarehouseTransferService $service)
    {
        try {
            $created = $service->handle(
                staff: $request->validated('staff'),
                products: $request->validated('products'),
                batchId: $request->validated('batch_id'),
            );
        } catch (InsufficientQuantityException|DomainException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json(['data' => $created], 201);
    }
}
