<?php
// app/Services/WarehouseTransferService.php

namespace App\Services;

use App\Enums\BatchStatus;
use App\Enums\SignerLevelStatus;
use App\Exceptions\InsufficientQuantityException;
use App\Models\BugalteriyaModel;
use App\Models\WarehouseBatch;
use App\Models\WarehouseItem;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class WarehouseTransferService
{
    /**
     * @param array $staff    Tanlangan xodim (snapshot)
     * @param array $products Tanlangan mahsulotlar (warehouse_item_id + quantity)
     * @param string $batchId  Oldindan yaratilgan partiyaning batch_number qiymati
     *
     * @return Collection<int, BugalteriyaModel>
     *
     * @throws InsufficientQuantityException
     * @throws DomainException
     */
    public function handle(array $staff, array $products, string $batchId): Collection
    {
        $products = collect($products)->sortBy('warehouse_item_id')->values()->all();

        return DB::transaction(function () use ($staff, $products, $batchId) {
            $batch = WarehouseBatch::where('batch_number', $batchId)->lockForUpdate()->firstOrFail();

            if ($batch->status !== BatchStatus::InProgress) {
                throw new DomainException('Bu batch allaqachon yopilgan yoki rad etilgan — mahsulot biriktirib bo\'lmaydi.');
            }

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

                /** @var WarehouseItem $item */
                $item = WarehouseItem::query()
                    ->whereKey($product['warehouse_item_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $item->load([
                    'informationItem.unit',
                    'warehouse.information.supplier',
                ]);

                $productName = $item->informationItem?->product_name ?? '';
                $available   = (float) $item->quantity;

                if ($available < $requested) {
                    throw new InsufficientQuantityException(
                        productName: $productName,
                        available: (int) $available,
                        requested: $requested,
                    );
                }

                $information = $item->warehouse?->information;

                $entry = BugalteriyaModel::create([
                    'batch_id'          => $batch->id,
                    'warehouse_item_id' => $item->id,

                    'name'             => $productName,
                    'document_number'  => $information?->contract_number,
                    'supplier_name'    => $information?->supplier?->name,

                    'type_id'          => $item->type_id,
                    'category_id'      => $item->category_id,
                    'model_id'         => $item->model_id,
                    'unit_id'          => $item->informationItem?->unit_id,

                    'quantity'         => $requested,

                    'room_name'        => $product['room_name'] ?? null,
                    'building'         => $product['building'] ?? null,
                    'room_number'      => $product['room_number'] ?? null,

                    'last_name'        => $staff['last_name'],
                    'first_name'       => $staff['first_name'],
                    'middle_name'      => $staff['middle_name'] ?? null,
                    'full_name'        => $staff['full_name'],
                    'department'       => $staff['department'] ?? null,

                    'condition'        => $product['condition'] ?? 'new',
                    'notes'            => $product['notes'] ?? null,

                    'status'           => BugalteriyaModel::STATUS_PENDING,
                ]);


                $affected = WarehouseItem::where('id', $item->id)
                    ->where('quantity', '>=', $requested)
                    ->decrement('quantity', $requested);

                if ($affected === 0) {
                    throw new InsufficientQuantityException(
                        productName: $productName,
                        available: (int) $item->fresh()->quantity,
                        requested: $requested,
                    );
                }

                $created->push($entry->load('type', 'category', 'model', 'unit'));
            }

            return $created;
        });
    }
}
