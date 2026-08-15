<?php
// app/Http/Controllers/Api/BugalteriyaController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseBatch\CompleteBugalteriyaRequest;
use App\Http\Resources\BugalteriyaResource;
use App\Models\BugalteriyaModel;
use App\Services\BugalteriyaService;
use DomainException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BugalteriyaController extends Controller
{
    /** MENYU 1 — ombordan kelgan, tasnif kutayotgan yozuvlar */
    public function awaitingClassification(): AnonymousResourceCollection
    {
        $entries = BugalteriyaModel::awaitingClassification()
            ->with(['warehouse', 'type', 'category', 'model', 'unit'])
            ->latest()
            ->paginate(20);

        return BugalteriyaResource::collection($entries);
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
