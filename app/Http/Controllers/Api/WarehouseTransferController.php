<?php
// app/Http/Controllers/Api/WarehouseTransferController.php

namespace App\Http\Controllers\Api;

use App\Exceptions\InsufficientQuantityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseBatch\StoreWarehouseTransferRequest;
use App\Services\WarehouseTransferService;
use DomainException;

class WarehouseTransferController extends Controller
{
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
