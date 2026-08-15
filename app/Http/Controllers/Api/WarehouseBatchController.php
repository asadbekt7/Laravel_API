<?php
// app/Http/Controllers/Api/WarehouseBatchController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseBatch\ApproveWarehouseBatchRequest;
use App\Http\Requests\WarehouseBatch\RejectWarehouseBatchRequest;
use App\Http\Requests\WarehouseBatch\SignAsAccountantRequest;
use App\Http\Requests\WarehouseBatch\StoreWarehouseBatchRequest;
use App\Http\Resources\WarehouseBatchResource;
use App\Models\WarehouseBatch;
use App\Services\WarehouseBatch\WarehouseBatchApprovalService;
use App\Services\WarehouseBatch\WarehouseBatchCreationService;
use App\Services\WarehouseBatch\WarehouseBatchSignService;
use DomainException;
use Illuminate\Database\QueryException;

class WarehouseBatchController extends Controller
{
    public function store(StoreWarehouseBatchRequest $request, WarehouseBatchCreationService $service)
    {
        try {
            $batch = $service->create(
                batchNumber: $request->validated('batch_number'),
                createdBy: $request->user()->id,
                signerIds: $request->validated('signer_ids'),
            );
        } catch (QueryException $e) {
            abort(422, 'Bu batch raqami allaqachon mavjud.');
        }

        return new WarehouseBatchResource($batch);
    }

    public function show(WarehouseBatch $batch): WarehouseBatchResource
    {
        $this->authorize('view', $batch);

        return new WarehouseBatchResource(
            $batch->load(['entries.warehouse', 'signers.user', 'createdBy'])
        );
    }

    public function items(WarehouseBatch $batch)
    {
        $this->authorize('view', $batch);

        return response()->json(['data' => $batch->items()]);
    }

    public function signAsAccountant(SignAsAccountantRequest $request, WarehouseBatch $batch, WarehouseBatchSignService $service)
    {
        try {
            $batch = $service->signAsAccountant($batch, $request->user(), $request->validated('entries'));
        } catch (DomainException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }

    public function approve(ApproveWarehouseBatchRequest $request, WarehouseBatch $batch, WarehouseBatchApprovalService $service)
    {
        try {
            $batch = $service->approve($batch, $request->user(), $request->validated('comment'));
        } catch (DomainException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }

    public function reject(RejectWarehouseBatchRequest $request, WarehouseBatch $batch, WarehouseBatchApprovalService $service)
    {
        try {
            $batch = $service->reject($batch, $request->user(), $request->validated('comment'));
        } catch (DomainException $e) {
            abort(422, $e->getMessage());
        }

        return new WarehouseBatchResource($batch);
    }
}
