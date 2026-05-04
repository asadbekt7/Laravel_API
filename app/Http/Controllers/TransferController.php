<?php

namespace App\Http\Controllers;

use App\Services\Transfer\MonitorTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function transferMonitor(
        Request $request,
        MonitorTransferService $service
    ): JsonResponse {
        $validated = $request->validate([
            'warehouse_id'     => 'required|integer|exists:warehouse,id',
            'inventory_number' => 'required|integer|exists:inventorynumbers,inventory_number',
            'room_name'        => 'required|string',
        ]);

        $result = $service->transfer(
            $validated['warehouse_id'],
            $validated['inventory_number'],
            $validated['room_name'],
        );

        return response()->json([
            'success' => true,
            'message' => 'Monitor muvaffaqiyatli ko\'chirildi.',
            'data'    => $result,
        ], 201);
    }

    public function transferPrinter(
        Request $request,
        PrinterTransferService $service
    ): JsonResponse {
        $validated = $request->validate([
            'warehouse_id'     => 'required|integer|exists:warehouse,id',
            'inventory_number' => 'required|integer|exists:inventorynumbers,inventory_number',
            'room_name'        => 'required|string',
        ]);

        $result = $service->transfer(
            $validated['warehouse_id'],
            $validated['inventory_number'],
            $validated['room_name'],
        );

        return response()->json([
            'success' => true,
            'message' => 'Printer muvaffaqiyatli ko\'chirildi.',
            'data'    => $result,
        ], 201);
    }
}
