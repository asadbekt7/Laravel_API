<?php
// App/Services/TelephoneTransferService.php
namespace App\Services;

use App\Exceptions\RoomApiException;
use App\Exceptions\TransferFailedException;
use App\Exceptions\WarehouseNotFoundException;
use App\Models\Telephonemodel;
use App\Models\Categorymodel;
use App\Repositories\Contracts\TelephoneRepositoryInterface;
use App\Repositories\Contracts\InventoryNumberRepositoryInterface;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use App\Services\Contracts\TelephoneTransferServiceInterface;
use App\Services\Contracts\RoomServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelephoneTransferService implements TelephoneTransferServiceInterface
{
    public function __construct(
        private readonly TelephoneRepositoryInterface       $telephoneRepository,
        private readonly WarehouseRepositoryInterface       $warehouseRepository,
        private readonly InventoryNumberRepositoryInterface $inventoryRepository,
        private readonly RoomServiceInterface               $roomService,
    ) {}

    public function transfer(int $warehouseId, int $inventoryNumber, string $roomName): Telephonemodel
    {
        $warehouse = $this->warehouseRepository->findOrFail($warehouseId);
        $telephoneCategory = Categorymodel::where('name', 'Telephone')->first();

        if (!$telephoneCategory) {
            throw new \RuntimeException("Bazada 'Telephone' kategoriyasi topilmadi.");
        }

        if ($warehouse->category_id !== $telephoneCategory->id) {
            throw new WarehouseNotFoundException(
                "Bu warehouse 'Telephone' kategoriyasiga tegishli emas."
            );
        }
        $roomData  = $this->roomService->getRoomData($roomName);

        try {
            return DB::transaction(function () use ($warehouse, $inventoryNumber, $roomData) {
                $inventoryRecord = $this->inventoryRepository->create($inventoryNumber);

                $telephone = $this->telephoneRepository->create([
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

                Log::info('Warehouse → Telephone ko\'chirish muvaffaqiyatli', [
                    'warehouse_id' => $warehouse->id,
                    'telephone_id' => $telephone->id,
                ]);

                return $telephone;
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
