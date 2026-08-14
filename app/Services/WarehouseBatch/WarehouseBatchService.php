<?php
// app/Services/WarehouseBatch/WarehouseBatchService.php
namespace App\Services\WarehouseBatch;

use App\DTOs\CreateWarehouseBatchDTO;
use App\Enums\SignerLevelStatus;
use App\Events\BatchSignerActivated;
use App\Exceptions\InsufficientQuantityException;
use App\Models\WarehouseBatch;
use App\Models\WarehouseModel;
use Illuminate\Support\Facades\DB;

class WarehouseBatchService
{
    public function create(CreateWarehouseBatchDTO $dto): WarehouseBatch
    {
        // Deadlock oldini olish uchun bir xil tartibda qulflaymiz
        $products = collect($dto->products)->sortBy('warehouse_id')->values()->all();

        return DB::transaction(function () use ($dto, $products) {
            $batch = WarehouseBatch::create([
                'staff_id' => $dto->staffId,
                'created_by' => $dto->createdBy,
            ]);

            // batch_number — ID asosida, race condition bo'lmasligi uchun (qo'shimcha lock kerak emas)
            $batch->update([
                'batch_number' => sprintf('WB-%s-%06d', now()->format('Y'), $batch->id),
            ]);

            foreach ($products as $product) {
                $requested = (int) $product['quantity'];

                $warehouse = WarehouseModel::lockForUpdate()->findOrFail($product['warehouse_id']);

                if ($warehouse->quantity < $requested) {
                    throw new InsufficientQuantityException(
                        productName: $warehouse->name,
                        available: $warehouse->quantity,
                        requested: $requested,
                    );
                }

                $batch->items()->create([
                    'warehouse_id' => $warehouse->id,
                    'quantity' => $requested,
                ]);

                $affected = WarehouseModel::where('id', $warehouse->id)
                    ->where('quantity', '>=', $requested)
                    ->decrement('quantity', $requested);

                if ($affected === 0) {
                    throw new InsufficientQuantityException(
                        productName: $warehouse->name,
                        available: $warehouse->fresh()->quantity,
                        requested: $requested,
                    );
                }
            }

            // Imzolovchilar: signerIds[0] = 1-daraja (buxgalter), darhol "active"
            foreach (array_values($dto->signerIds) as $index => $userId) {
                $level = $index + 1;

                $batch->signers()->create([
                    'user_id' => $userId,
                    'level' => $level,
                    'status' => $level === 1 ? SignerLevelStatus::Active : SignerLevelStatus::Pending,
                ]);
            }

            $firstSigner = $batch->signers()->where('level', 1)->first();
            event(new BatchSignerActivated($firstSigner));

            return $batch->load(['items.warehouse', 'signers.user', 'staff']);
        });
    }
}
