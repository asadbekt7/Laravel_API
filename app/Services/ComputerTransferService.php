<?php

namespace App\Services;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Models\Computermodel;
use App\Repositories\Contracts\ComputerRepositoryInterface;
use App\Repositories\Contracts\InventoryNumberRepositoryInterface;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use App\Services\Contracts\ComputerTransferServiceInterface;
use App\Services\Contracts\RoomServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComputerTransferService implements ComputerTransferServiceInterface
{
    public function __construct(
        private readonly ComputerRepositoryInterface        $computerRepository,
        private readonly WarehouseRepositoryInterface       $warehouseRepository,
        private readonly InventoryNumberRepositoryInterface $inventoryRepository,
        private readonly RoomServiceInterface               $roomService,
    ) {}

    public function transfer(int $warehouseId, int $inventoryNumber, string $roomName): Computermodel
    {
        // STEP 1 — Warehouse ma'lumotini olish
        $warehouse = $this->warehouseRepository->findOrFail($warehouseId);

        // STEP 2 — Room API dan ma'lumot olish
        $roomData = $this->roomService->getRoomData($roomName);

        try {
            return DB::transaction(function () use ($warehouse, $inventoryNumber, $roomData) {

                // STEP 3 — Inventory number yaratish
                $inventoryRecord = $this->inventoryRepository->create($inventoryNumber);

                // STEP 4 — Computers jadvaliga yozish
                $computer = $this->computerRepository->create([
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

                // STEP 5 — Warehouse dan o'chirish
                $this->warehouseRepository->delete($warehouse);

                Log::info('Warehouse → Computer ko\'chirish muvaffaqiyatli', [
                    'warehouse_id' => $warehouse->id,
                    'computer_id'  => $computer->id,
                ]);

                return $computer;
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
