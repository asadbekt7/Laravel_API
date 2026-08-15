<?php
// app/Services/BugalteriyaService.php

namespace App\Services;

use App\Enums\ItemType;
use App\Models\BugalteriyaModel;
use App\Models\ItemsModel;
use App\Models\WarehouseModel;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BugalteriyaService
{
    /**
     * MENYU 1 — buxgalter turni tanlab, maydonlarni to'ldirib tasdiqlaydi.
     *
     * @return Collection<int, ItemsModel>
     */
    public function complete(BugalteriyaModel $entry, array $data): Collection
    {
        $itemType = $data['item_type'] instanceof ItemType
            ? $data['item_type']
            : ItemType::from($data['item_type']);

        return DB::transaction(function () use ($entry, $data, $itemType) {
            // Avval qulflaymiz, KEYIN statusni tekshiramiz — ikki marta
            // "complete" bosilsa ham, ikkinchisi shu yerda to'xtaydi.
            $entry = BugalteriyaModel::whereKey($entry->id)->lockForUpdate()->firstOrFail();

            if (! $entry->isPending()) {
                throw new DomainException(
                    "Bu yozuv allaqachon yakunlangan yoki bekor qilingan (status: {$entry->status})."
                );
            }

            $created = collect();

            if ($itemType === ItemType::ASOSIY_VOSITA) {
                $expiryDate = $data['expiry_date'] ?? null;
                $inventoryNumbers = $data['inventory_numbers'] ?? [];

                if (empty($expiryDate)) {
                    throw new DomainException('Asosiy vosita uchun yaroqlilik muddati majburiy.');
                }
                if (count($inventoryNumbers) !== (int) $entry->quantity) {
                    throw new DomainException(
                        'Inventar raqamlari soni mahsulot miqdoriga teng bo\'lishi kerak.'
                    );
                }

                foreach ($inventoryNumbers as $inventoryNumber) {
                    $created->push(
                        $this->makeItem($entry, [
                            'item_type' => $itemType,
                            'expiry_date' => $expiryDate,
                            'statya' => null,
                            'quantity' => 1,
                            'inventory_number' => $inventoryNumber,
                        ])
                    );
                }
            } else {
                $statya = $data['statya'] ?? null;
                if (empty($statya)) {
                    throw new DomainException('Rasxod uchun statya majburiy.');
                }

                $created->push(
                    $this->makeItem($entry, [
                        'item_type' => $itemType,
                        'expiry_date' => null,
                        'statya' => $statya,
                        'quantity' => (int) $entry->quantity,
                        'inventory_number' => null,
                    ])
                );
            }

            $entry->update([
                'item_type' => $itemType,
                'expiry_date' => $data['expiry_date'] ?? null,
                'statya' => $data['statya'] ?? null,
                'status' => BugalteriyaModel::STATUS_COMPLETED,
                'items_id' => $created->first()?->id,
                'completed_at' => now(),
            ]);

            return $created;
        });
    }

    private function makeItem(BugalteriyaModel $entry, array $overrides): ItemsModel
    {
        return ItemsModel::create([
            'name' => $entry->name,
            'document_number' => $entry->document_number,
            'supplier_name' => $entry->supplier_name,
            'item_type' => $overrides['item_type'],
            'expiry_date' => $overrides['expiry_date'],
            'statya' => $overrides['statya'],
            'type_id' => $entry->type_id,
            'category_id' => $entry->category_id,
            'model_id' => $entry->model_id,
            'unit_id' => $entry->unit_id,
            'quantity' => $overrides['quantity'],
            'inventory_number' => $overrides['inventory_number'],
            'room_name' => $entry->room_name,
            'building' => $entry->building,
            'room_number' => $entry->room_number,
            'last_name' => $entry->last_name,
            'first_name' => $entry->first_name,
            'middle_name' => $entry->middle_name,
            'full_name' => $entry->full_name,
            'department' => $entry->department,
            'condition' => $entry->condition,
            'status' => 'active',
            'notes' => $entry->notes,
        ])->load('type', 'category', 'model', 'unit');
    }

    /** Bekor qilish — miqdor omborga qaytariladi. */
    public function cancel(BugalteriyaModel $entry): void
    {
        DB::transaction(function () use ($entry) {
            $entry = BugalteriyaModel::whereKey($entry->id)->lockForUpdate()->firstOrFail();

            if (! $entry->isPending()) {
                throw new DomainException('Faqat pending holatdagi yozuvni bekor qilish mumkin.');
            }

            if ($entry->warehouse_id) {
                WarehouseModel::where('id', $entry->warehouse_id)
                    ->increment('quantity', $entry->quantity);
            }

            $entry->update(['status' => BugalteriyaModel::STATUS_CANCELLED]);
        });
    }
}
