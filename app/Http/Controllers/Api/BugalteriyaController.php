<?php
// app/Http/Controllers/Api/BugalteriyaController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseBatch\CompleteBugalteriyaRequest;
use App\Http\Resources\BugalteriyaResource;
use App\Models\BugalteriyaModel;
use App\Services\BugalteriyaService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BugalteriyaController extends Controller
{
    /** MENYU 1 — ombordan kelgan, tasnif kutayotgan yozuvlar */
    public function index(Request $request): AnonymousResourceCollection
    {
        $status = $request->input('status', BugalteriyaModel::STATUS_PENDING);
        $allowed = [
            BugalteriyaModel::STATUS_PENDING,
            BugalteriyaModel::STATUS_COMPLETED,
            BugalteriyaModel::STATUS_CANCELLED,
        ];

        $entries = BugalteriyaModel::query()
            ->when(in_array($status, $allowed, true), fn ($q) => $q->where('status', $status))
            ->with(['warehouseItem.warehouse', 'type', 'category', 'model', 'unit', 'batch'])
            ->latest()
            ->paginate((int) $request->input('per_page', 20));

        return BugalteriyaResource::collection($entries);
    }

    public function show(BugalteriyaModel $bugalteriya): BugalteriyaResource
    {
        return new BugalteriyaResource(
            $bugalteriya->load(['warehouseItem.warehouse', 'type', 'category', 'model', 'unit', 'batch'])
        );
    }

    public function complete(CompleteBugalteriyaRequest $request, BugalteriyaModel $bugalteriya, BugalteriyaService $service)
    {
        try {
            $items = $service->complete($bugalteriya, $request->validated());
        } catch (DomainException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json(['data' => $items]);
    }

    public function cancel(BugalteriyaModel $bugalteriya, BugalteriyaService $service)
    {
        try {
            $service->cancel($bugalteriya);
        } catch (DomainException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json(['message' => 'Bekor qilindi.']);
    }
}
