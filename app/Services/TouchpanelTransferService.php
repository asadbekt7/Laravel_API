<?php
// App/Services/TouchpanelTransferService.php
namespace App\Services;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Models\Touchpanelmodel;
use App\Models\Categorymodel;
use App\Repositories\Contracts\TouchpanelRepositoryInterface;
use App\Repositories\Contracts\InventoryNumberRepositoryInterface;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use App\Services\Contracts\TouchpanelTransferServiceInterface;
use App\Services\Contracts\RoomServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TouchpanelTransferService implements TouchpanelTransferServiceInterface
{
    public function __construct(
        private readonly TouchpanelRepositoryInterface      $touchpanelRepository,
        private readonly WarehouseRepositoryInterface       $warehouseRepository,
        private readonly InventoryNumberRepositoryInterface $inventoryRepository,
        private readonly RoomServiceInterface               $roomService,
    ) {}

    public function transfer(int $warehouseId, int $inventoryNumber, string $roomName): Touchpanelmodel
    {
        $warehouse = $this->warehouseRepository->findOrFail($warehouseId);
        $touchpanelCategory = Categorymodel::where('name', 'Touchpanel')->first();

        if (!$touchpanelCategory) {
            throw new \RuntimeException("Bazada 'Touchpanel' kategoriyasi topilmadi.");
        }

        if ($warehouse->category_id !== $touchpanelCategory->id) {
            throw new WarehouseNotFoundException(
                "Bu warehouse 'Touchpanel' kategoriyasiga tegishli emas."
            );
        }
        $roomData  = $this->roomService->getRoomData($roomName);

        try {
            return DB::transaction(function () use ($warehouse, $inventoryNumber, $roomData) {
                $inventoryRecord = $this->inventoryRepository->create($inventoryNumber);

                $touchpanel = $this->touchpanelRepository->create([
                    'name'                => $warehouse->name,
                    'category_id'         => $warehouse->category_id,
                    'model_id'            => $warehouse->model_id,
                    'unit_id'             => $warehouse->unit_id,
                    'quantity'            => $warehouse->quantity,
                    'description'         => $warehouse->description,
                    'inventory_number_id' => $inventoryRecord->id,
                    'room_name'           => $roomData['room_name'],
                    'room_number'         => $roomData['room_number'],
                    'building'            => $roomData['building'],
                    'employee_id'         => null,
                ]);

                $this->warehouseRepository->delete($warehouse);

                Log::info('Warehouse → Touchpanel ko\'chirish muvaffaqiyatli', [
                    'warehouse_id'  => $warehouse->id,
                    'touchpanel_id' => $touchpanel->id,
                ]);

                return $touchpanel;
            });

        } catch (WarehouseNotFoundException | RoomApiException $e) {
            throw $e;
        } catch (\Illuminate\Database\QueryException $e) {
            Log::warning('Inventory number takrorlandi', ['inventory_number' => $inventoryNumber]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Transfer tranzaksiyasi muvaffaqiyatsiz tugadi', [
                'warehouse_id' => $warehouse->id,
                'error'        => $e->getMessage(),
            ]);
            throw TransferFailedException::fromPrevious($e);
        }
    }
}
