<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteBugalteriyaRequest;
use App\Models\BugalteriyaModel;
use App\Services\BugalteriyaService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BugalteriyaController extends Controller
{
    public function __construct(
        private readonly BugalteriyaService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $entries = BugalteriyaModel::with('type', 'category', 'model', 'unit')
            ->when(
                $request->query('status', 'pending'),
                fn ($q, $status) => $q->where('status', $status)
            )
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json($entries);
    }

    public function show(BugalteriyaModel $bugalteriya): JsonResponse
    {
        return response()->json(
            $bugalteriya->load('type', 'category', 'model', 'unit', 'item')
        );
    }

    public function complete(
        CompleteBugalteriyaRequest $request,
        BugalteriyaModel $bugalteriya,
    ): JsonResponse {
        try {
            $items = $this->service->complete($bugalteriya, $request->validated());
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => "{$items->count()} ta yozuv items ga saqlandi.",
            'data'    => $items,
        ]);
    }

    public function cancel(BugalteriyaModel $bugalteriya): JsonResponse
    {
        try {
            $this->service->cancel($bugalteriya);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Yozuv bekor qilindi, miqdor omborga qaytarildi.']);
    }
}
