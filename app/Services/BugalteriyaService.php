<?php

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
     * Buxgalter turni tanlab, maydonlarni to'ldirib tasdiqlaydi.
     *
     *  - ASOSIY VOSITA: har bir dona alohida item bo'ladi (quantity = 1),
     *    har biri o'z inventar raqami bilan. Ya'ni miqdor 3 bo'lsa -> 3 ta item.
     *  - RASXOD: yaxlit bitta item (quantity = to'liq son), faqat statya.
     *
     * @return Collection<int, ItemsModel>  Yaratilgan item(lar)
     */
    public function complete(BugalteriyaModel $entry, array $data): Collection
    {
        if (! $entry->isPending()) {
            throw new DomainException(
                "Bu yozuv allaqachon yakunlangan yoki bekor qilingan (status: {$entry->status})."
            );
        }

        $itemType = $data['item_type'] instanceof ItemType
            ? $data['item_type']
            : ItemType::from($data['item_type']);

        return DB::transaction(function () use ($entry, $data, $itemType) {
            $created = collect();

            if ($itemType === ItemType::ASOSIY_VOSITA) {
                $expiryDate        = $data['expiry_date'] ?? null;
                $inventoryNumbers  = $data['inventory_numbers'] ?? [];

                if (empty($expiryDate)) {
                    throw new DomainException('Asosiy vosita uchun yaroqlilik muddati majburiy.');
                }
                if (count($inventoryNumbers) !== (int) $entry->quantity) {
                    throw new DomainException(
                        'Inventar raqamlari soni mahsulot miqdoriga teng bo\'lishi kerak.'
                    );
                }

                // Har bir dona -> alohida item (quantity = 1)
                foreach ($inventoryNumbers as $inventoryNumber) {
                    $created->push(
                        $this->makeItem($entry, [
                            'item_type'        => $itemType,
                            'expiry_date'      => $expiryDate,
                            'statya'           => null,
                            'quantity'         => 1,
                            'inventory_number' => $inventoryNumber,
                        ])
                    );
                }
            } else { // RASXOD — yaxlit
                $statya = $data['statya'] ?? null;
                if (empty($statya)) {
                    throw new DomainException('Rasxod uchun statya majburiy.');
                }

                $created->push(
                    $this->makeItem($entry, [
                        'item_type'        => $itemType,
                        'expiry_date'      => null,
                        'statya'           => $statya,
                        'quantity'         => (int) $entry->quantity,
                        'inventory_number' => null,
                    ])
                );
            }

            // Yozuvni yakunlangan deb belgilaymiz.
            // Bir nechta item bo'lishi mumkin -> birinchisini referens sifatida saqlaymiz.
            $entry->update([
                'item_type'    => $itemType,
                'expiry_date'  => $data['expiry_date'] ?? null,
                'statya'       => $data['statya'] ?? null,
                'status'       => BugalteriyaModel::STATUS_COMPLETED,
                'items_id'     => $created->first()?->id,
                'completed_at' => now(),
            ]);

            return $created;
        });
    }

    /**
     * Bitta item yaratish (bugalteriya snapshot'idan).
     */
    private function makeItem(BugalteriyaModel $entry, array $overrides): ItemsModel
    {
        return ItemsModel::create([
            'name'             => $entry->name,
            'document_number'  => $entry->document_number,
            'supplier_name'    => $entry->supplier_name,

            'item_type'        => $overrides['item_type'],
            'expiry_date'      => $overrides['expiry_date'],
            'statya'           => $overrides['statya'],

            'type_id'          => $entry->type_id,
            'category_id'      => $entry->category_id,
            'model_id'         => $entry->model_id,
            'unit_id'          => $entry->unit_id,

            'quantity'         => $overrides['quantity'],
            'inventory_number' => $overrides['inventory_number'],

            'room_name'        => $entry->room_name,
            'building'         => $entry->building,
            'room_number'      => $entry->room_number,

            'last_name'        => $entry->last_name,
            'first_name'       => $entry->first_name,
            'middle_name'      => $entry->middle_name,
            'full_name'        => $entry->full_name,
            'department'       => $entry->department,

            'condition'        => $entry->condition,
            'status'           => 'active',
            'notes'            => $entry->notes,
        ])->load('type', 'category', 'model', 'unit');
    }

    /**
     * Bekor qilish — miqdor omborga qaytariladi.
     */
    public function cancel(BugalteriyaModel $entry): void
    {
        if (! $entry->isPending()) {
            throw new DomainException('Faqat pending holatdagi yozuvni bekor qilish mumkin.');
        }

        DB::transaction(function () use ($entry) {
            if ($entry->warehouse_id) {
                WarehouseModel::where('id', $entry->warehouse_id)
                    ->increment('quantity', $entry->quantity);
            }

            $entry->update(['status' => BugalteriyaModel::STATUS_CANCELLED]);
        });
    }
}
