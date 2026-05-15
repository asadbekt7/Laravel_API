<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Services\Transfer\TransferService;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    public function __construct(
        private readonly TransferService $transferService
    ) {}

    /**
     * POST /api/transfers
     */
    public function store(TransferRequest $request): JsonResponse
    {
        $result = $this->transferService->transferBatch(
            $request->input('items')
        );

        $hasFailures = count($result['failed']) > 0;
        $hasSuccess  = count($result['success']) > 0;

        $status = match (true) {
            $hasSuccess && !$hasFailures => 200,
            $hasSuccess && $hasFailures  => 207,
            default                      => 422,
        };

        return response()->json([
            'message' => $this->resolveMessage($result),
            'data'    => $result,
        ], $status);
    }

    private function resolveMessage(array $result): string
    {
        $s = count($result['success']);
        $f = count($result['failed']);

        if ($f === 0) return "{$s} ta yozuv muvaffaqiyatli transfer qilindi";
        if ($s === 0) return "Transfer amalga oshmadi ({$f} ta xato)";
        return "{$s} ta transfer qilindi, {$f} ta xato yuz berdi";
    }
}
