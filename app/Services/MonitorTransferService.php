<?php
// App/Services/MonitorTransferService.php
namespace App\Services;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Models\Monitormodel;
use App\Models\CategoryModel;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use App\Repositories\Contracts\InventoryNumberRepositoryInterface;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use App\Services\Contracts\MonitorTransferServiceInterface;
use App\Services\Contracts\RoomServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorTransferService implements MonitorTransferServiceInterface
{
    public function __construct(
        private readonly MonitorRepositoryInterface         $monitorRepository,
        private readonly WarehouseRepositoryInterface       $warehouseRepository,
        private readonly InventoryNumberRepositoryInterface $inventoryRepository,
        private readonly RoomServiceInterface               $roomService,
    ) {}

    public function transfer(int $warehouseId, int $inventoryNumber, string $roomName): Monitormodel
    {
        $warehouse = $this->warehouseRepository->findOrFail($warehouseId);
        $monitorCategory = CategoryModel::where('name', 'Device')->first();  // 2 — kategoriya topish

        if (!$monitorCategory) {
            throw new \RuntimeException("Bazada 'Device' kategoriyasi topilmadi.");
        }

        if ($warehouse->category_id !== $monitorCategory->id) {
            throw new WarehouseNotFoundException(
                "Bu warehouse 'Device' kategoriyasiga tegishli emas."
            );
        }
        $roomData  = $this->roomService->getRoomData($roomName);

        try {
            return DB::transaction(function () use ($warehouse, $inventoryNumber, $roomData) {
                $inventoryRecord = $this->inventoryRepository->create($inventoryNumber);

                $monitor = $this->monitorRepository->create([
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

                Log::info('Warehouse → Monitor ko\'chirish muvaffaqiyatli', [
                    'warehouse_id' => $warehouse->id,
                    'monitor_id'   => $monitor->id,
                ]);

                return $monitor;
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
