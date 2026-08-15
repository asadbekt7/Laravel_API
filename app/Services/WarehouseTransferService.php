<?php
// app/Services/WarehouseTransferService.php

namespace App\Services;

use App\Enums\BatchStatus;
use App\Enums\SignerLevelStatus;
use App\Exceptions\InsufficientQuantityException;
use App\Models\BugalteriyaModel;
use App\Models\WarehouseBatch;
use App\Models\WarehouseModel;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WarehouseTransferService
{
    /**
     * @param array $staff    Tanlangan xodim (snapshot)
     * @param array $products Tanlangan mahsulotlar
     * @param int   $batchId  "POST /warehouse-batches" orqali oldindan yaratilgan batch ID'si
     *
     * @return Collection<int, BugalteriyaModel>
     *
     * @throws InsufficientQuantityException
     * @throws DomainException
     */
    public function handle(array $staff, array $products, int $batchId): Collection
    {
        $products = collect($products)->sortBy('warehouse_id')->values()->all();

        return DB::transaction(function () use ($staff, $products, $batchId) {
            $batch = WarehouseBatch::lockForUpdate()->findOrFail($batchId);

            if ($batch->status !== BatchStatus::InProgress) {
                throw new DomainException('Bu batch allaqachon yopilgan yoki rad etilgan — mahsulot biriktirib bo\'lmaydi.');
            }

            // === BUG FIX ===
            // Bugalter (1-daraja) allaqachon imzolab bo'lgan bo'lsa, batch endi 2-daraja
            // (yoki undan keyingi) tasdiqlashni kutmoqda — status hali "InProgress" bo'lib
            // turadi. Shu holatda yangi mahsulot biriktirilsa, u bugalter nazoratidan
            // chetlab o'tib, debit/kredit'siz PDF'ga tushib qolardi.
            $accountantIsActive = $batch->signers()
                ->where('level', 1)
                ->where('status', SignerLevelStatus::Active)
                ->exists();

            if (! $accountantIsActive) {
                throw new DomainException('Bugalter ushbu batchni allaqachon tasdiqlagan — endi yangi mahsulot qo\'shib bo\'lmaydi. Yangi batch oching.');
            }

            $created = collect();

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

                $warehouse->load('information.supplier');

                $entry = BugalteriyaModel::create([
                    'batch_id'         => $batch->id,
                    'warehouse_id'     => $warehouse->id,

                    'name'             => $warehouse->name,
                    'document_number'  => $warehouse->information?->contract_number,
                    'supplier_name'    => $warehouse->information?->supplier?->name,

                    'type_id'          => $warehouse->type_id,
                    'category_id'      => $warehouse->category_id,
                    'model_id'         => $warehouse->model_id,
                    'unit_id'          => $warehouse->unit_id,

                    'quantity'         => $requested,

                    'room_name'        => $product['room_name'] ?? null,
                    'building'         => $product['building'] ?? null,
                    'room_number'      => $product['room_number'] ?? null,

                    'last_name'        => $staff['last_name'],
                    'first_name'       => $staff['first_name'],
                    'middle_name'      => $staff['middle_name'] ?? null,
                    'full_name'        => $staff['full_name'],
                    'department'       => $staff['department'] ?? null,

                    'condition'        => $product['condition'] ?? $warehouse->condition ?? 'new',
                    'notes'            => $product['notes'] ?? null,

                    'status'           => BugalteriyaModel::STATUS_PENDING,
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

                $created->push($entry->load('type', 'category', 'model', 'unit'));
            }

            return $created;
        });
    }
}
