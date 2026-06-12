<?php

namespace App\Http\Controllers;

use App\Exceptions\RoomApiException;
use App\Models\ItemsModel;
use App\Services\RoomApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

        } catch (RoomApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 503);
        }

        $data = $this->mergeRoomsWithItemStats($rooms);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
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

    private function mergeRoomsWithItemStats(array $rooms): array
    {
        $itemStats = $this->getItemStatsMap();

        return array_map(function (array $room) use ($itemStats) {
            $apiRoomName = $room['name'] ?? $room['room_name'] ?? null;

            $key = $apiRoomName !== null
                ? mb_strtolower(trim($apiRoomName))
                : null;

            $stat = $key !== null ? $itemStats->get($key) : null;

            $room['total_items'] = $stat?->total_items ?? 0;

            return $room;
        }, $rooms);
    }

    private function getItemStatsMap(): Collection
    {
        return ItemsModel::query()
            ->whereNull('deleted_at')
            ->selectRaw('room_name, building, COUNT(*) as total_items')
            ->groupBy('room_name', 'building')
            ->get()
            ->keyBy(fn ($item) => mb_strtolower(trim($item->room_name)));
    }
}
