<?php

namespace App\Services;

use App\Enums\ItemType;
use App\Models\BugalteriyaModel;
use App\Models\ItemsModel;
use App\Models\WarehouseModel;
use DomainException;
use Illuminate\Support\Facades\DB;

class BugalteriyaService
{
    /**
     * Buxgalter turi bo'yicha maydonlarni to'ldirib tasdiqlaydi:
     *  - asosiy vosita: expiry_date + inventory_number (majburiy)
     *  - rasxod:        statya (majburiy), boshqa hech narsa
     * Yozuv items jadvaliga o'tadi.
     */
    public function complete(BugalteriyaModel $entry, array $data): ItemsModel
    {
        if (! $entry->isPending()) {
            throw new DomainException(
                "Bu yozuv allaqachon yakunlangan yoki bekor qilingan (status: {$entry->status})."
            );
        }

        // Turi bo'yicha buxgalter kiritadigan maydonlar
        if ($entry->item_type === ItemType::ASOSIY_VOSITA) {
            $expiryDate      = $data['expiry_date'] ?? null;
            $inventoryNumber = $data['inventory_number'] ?? null;
            $statya          = null;

            if (empty($expiryDate)) {
                throw new DomainException('Asosiy vosita uchun yaroqlilik muddati majburiy.');
            }
            if (empty($inventoryNumber)) {
                throw new DomainException('Asosiy vosita uchun inventar raqami majburiy.');
            }
        } else { // RASXOD
            $statya          = $data['statya'] ?? null;
            $expiryDate      = null;
            $inventoryNumber = null; // rasxodda inventar raqam yo'q

            if (empty($statya)) {
                throw new DomainException('Rasxod uchun statya majburiy.');
            }
        }

        return DB::transaction(function () use ($entry, $expiryDate, $inventoryNumber, $statya) {
            $item = ItemsModel::create([
                'name'             => $entry->name,
                'document_number'  => $entry->document_number,
                'supplier_name'    => $entry->supplier_name,

                'item_type'        => $entry->item_type,
                'expiry_date'      => $expiryDate,
                'expense_item'     => $statya,

                'type_id'          => $entry->type_id,
                'category_id'      => $entry->category_id,
                'model_id'         => $entry->model_id,
                'unit_id'          => $entry->unit_id,

                'quantity'         => $entry->quantity,
                'inventory_number' => $inventoryNumber,

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
            ]);

            $entry->update([
                'expiry_date'      => $expiryDate,
                'inventory_number' => $inventoryNumber,
                'statya'           => $statya,
                'status'           => BugalteriyaModel::STATUS_COMPLETED,
                'items_id'         => $item->id,
                'completed_at'     => now(),
            ]);

            return $item->load('type', 'category', 'model', 'unit');
        });
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
