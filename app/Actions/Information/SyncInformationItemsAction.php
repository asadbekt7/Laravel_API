<?php

declare(strict_types=1);

namespace App\Actions\Information;

use App\DTOs\InformationItemData;
use App\Models\InformationModel;

/**
 * Update paytida kelgan items massivini mavjud itemlar bilan solishtiradi:
 *  - payload'da yo'q itemlar o'chiriladi,
 *  - id bor itemlar yangilanadi,
 *  - id yo'q itemlar yangi qo'shiladi.
 *
 * updateOrCreate() ataylab tanlangan: u save() orqali ishlaydi, shuning
 * uchun InformationItem::booted()dagi total_price avto-hisoblash ishlaydi.
 * (Xom `update()` query builder orqali bo'lsa, model event'lari chaqirilmas
 * va total_price eskirib qolar edi — bu keng tarqalgan xato.)
 */
final readonly class SyncInformationItemsAction
{
    /**
     * @param  InformationItemData[]  $items
     */
    public function execute(InformationModel $information, array $items): void
    {
        $incomingIds = array_values(array_filter(array_map(fn ($i) => $i->id, $items)));

        $information->items()
            ->when($incomingIds !== [], fn ($q) => $q->whereNotIn('id', $incomingIds), fn ($q) => $q)
            ->delete();

        foreach ($items as $item) {
            $information->items()->updateOrCreate(
                ['id' => $item->id ?? 0],
                [
                    'product_name' => $item->productName,
                    'unit_id'      => $item->unitId,
                    'quantity'     => $item->quantity,
                    'item_price'   => $item->itemPrice,
                ]
            );
        }
    }
}
