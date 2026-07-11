<?php

namespace App\Services;

use App\Exceptions\InsufficientQuantityException;
use App\Models\ItemsModel;
use App\Models\WarehouseModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WarehouseTransferService
{
    /**
     * Ombordan mahsulotlarni xodimga biriktirish (transfer).
     *
     * @param array $staff    Tanlangan xodim (StaffApiService natijasidan snapshot)
     * @param array $products Tanlangan mahsulotlar
     *
     * @return Collection<int, ItemModel>
     *
     * @throws InsufficientQuantityException
     */
    public function handle(array $staff, array $products): Collection
    {
        // Deadlock oldini olish: qulflash tartibi doim bir xil
        $products = collect($products)
            ->sortBy('warehouse_id')
            ->values()
            ->all();

        return DB::transaction(function () use ($staff, $products) {
            $created = collect();

            foreach ($products as $product) {
                $requested = (int) $product['quantity'];

                $warehouse = WarehouseModel::lockForUpdate()
                    ->findOrFail($product['warehouse_id']);

                if ($warehouse->quantity < $requested) {
                    throw new InsufficientQuantityException(
                        productName: $warehouse->name,
                        available: $warehouse->quantity,
                        requested: $requested,
                    );
                }

                $warehouse->load('information.supplier');

                $item = ItemsModel::create([
                    'name'             => $warehouse->name,
                    'document_number'  => $warehouse->information?->contract_number,
                    'supplier_name'    => $warehouse->information?->supplier?->name,

                    'type_id'          => $warehouse->type_id,
                    'category_id'      => $warehouse->category_id,
                    'model_id'         => $warehouse->model_id,
                    'unit_id'          => $warehouse->unit_id,

                    'quantity'         => $requested,
                    'inventory_number' => $product['inventory_number'],

                    'room_name'        => $product['room_name'] ?? null,
                    'building'         => $product['building'] ?? null,
                    'room_number'      => $product['room_number'] ?? null,

                    'last_name'        => $staff['last_name'],
                    'first_name'       => $staff['first_name'],
                    'middle_name'      => $staff['middle_name'] ?? null,
                    'full_name'        => $staff['full_name'],
                    'department'       => $staff['department'] ?? null,

                    'condition'        => $product['condition'] ?? $warehouse->condition ?? 'new',
                    'status'           => 'active',
                    'notes'            => $product['notes'] ?? null,
                ]);

                // SQL darajasidagi shartli kamaytirish — ikkinchi qavat himoya
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

                $created->push($item->load('type', 'category', 'model', 'unit'));
            }

            return $created;
        });
    }
}
