<?php
// App/Actions/Touchpanel/TransferWarehouseToTouchpanelAction.php
namespace App\Actions\Touchpanel;

use App\Models\Warehousemodel;
use App\Models\Touchpanelmodel;
use App\Models\Inventorynumbermodel;
use App\Services\External\RoomService;
use App\Services\External\EmployeeService;
use App\Exceptions\RoomApiException;
use App\Exceptions\WarehouseTransferException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferWarehouseToTouchpanelAction
{
    public function __construct(
        private readonly RoomService     $roomService,
        private readonly EmployeeService $employeeService,
    ) {}

    public function execute(
        array  $warehouseIds,
        string $inventoryNumber,
        ?int   $roomId = null,
        ?int   $employeeId = null,
    ): array {
        $roomData = $this->fetchRoomData($roomId);
        return DB::transaction(function () use ($warehouseIds, $inventoryNumber, $roomData): array {
            if (Inventorynumbermodel::where('inventory_number', $inventoryNumber)->exists()) {
                throw new WarehouseTransferException("Bu inventory raqam allaqachon mavjud.");
            }
            $inventoryRecord = Inventorynumbermodel::create(['inventory_number' => $inventoryNumber]);
            $warehouseItems = Warehousemodel::lockForUpdate()->findMany($warehouseIds);
            $missingIds = array_diff($warehouseIds, $warehouseItems->pluck('id')->toArray());
            if (!empty($missingIds)) {
                throw new WarehouseTransferException('Topilmadi: ' . implode(', ', $missingIds));
            }
            $touchpanels = [];
            foreach ($warehouseItems as $item) {
                $touchpanels[] = Touchpanelmodel::create([
                    'name'                => $item->name,
                    'category_id'         => $item->category_id,
                    'model_id'            => $item->model_id,
                    'unit_id'             => $item->unit_id,
                    'quantity'            => $item->quantity,
                    'description'         => $item->description,
                    'room_name'           => $roomData['room_name']   ?? null,
                    'building'            => $roomData['building']    ?? null,
                    'room_number'         => $roomData['room_number'] ?? null,
                    'inventory_number_id' => $inventoryRecord->id,
                    'inventory_number'    => $inventoryRecord->inventory_number,
                    'warehouse_id'        => $item->id,
                ]);
                $item->delete();
            }
            return $touchpanels;
        });
    }

    private function fetchRoomData(?int $roomId): ?array
    {
        if (!$roomId) return null;
        try {
            return $this->roomService->getRoomById($roomId);
        } catch (RoomApiException $e) {
            Log::warning('Room API ishlamadi, null bilan davom etiladi', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
