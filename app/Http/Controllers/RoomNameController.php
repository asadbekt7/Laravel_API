<?php

namespace App\Http\Controllers;

use App\Exceptions\RoomApiException;
use App\Models\ItemsModel;
use App\Services\RoomApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomNameController extends Controller
{
    public function __construct(
        private readonly RoomApiService $roomApiService
    ) {}
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search', '');

        try {
            $rooms = $this->roomApiService->getAllRooms($search);

            return response()->json([
                'success' => true,
                'data'    => $rooms,
            ]);

        } catch (RoomApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 503);
        }
    }
    public function items(string $roomName): JsonResponse
    {
        $items = ItemsModel::query()
            ->whereNull('deleted_at')
            ->where('room_name', $roomName)
            ->selectRaw('room_name, building, COUNT(*) as total_items')
            ->groupBy('room_name', 'building')
            ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "'{$roomName}' xonasida hech qanday buyum topilmadi.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }
    public function show(string $roomName): JsonResponse
    {
        try {
            $roomData = $this->roomApiService->getRoomData($roomName);

        } catch (RoomApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 503);
        }

        $stats = ItemsModel::query()
            ->whereNull('deleted_at')
            ->where('room_name', $roomName)
            ->selectRaw('room_name, building, COUNT(*) as total_items')
            ->groupBy('room_name', 'building')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'room'        => $roomData,
                'total_items' => $stats?->total_items ?? 0,
            ],
        ]);
    }
}
