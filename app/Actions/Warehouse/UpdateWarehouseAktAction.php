<?php

declare(strict_types=1);

namespace App\Actions\Warehouse;

use App\DTOs\Warehouse\WarehouseUpdateData;
use App\Models\WarehouseModel;

final readonly class UpdateWarehouseAktAction
{
    public function execute(WarehouseModel $warehouse, WarehouseUpdateData $data): WarehouseModel
    {
        // Accept/Reject Action'lardagi kabi - qatorni lockForUpdate() bilan
        // qayta o'qiymiz, shu orqali ikki xodim bir vaqtda bitta aktni
        // tahrirlaganda bir-birining o'zgarishini bosib ketishining oldi olinadi.
        /** @var WarehouseModel $warehouse */
        $warehouse = WarehouseModel::query()
            ->whereKey($warehouse->id)
            ->lockForUpdate()
            ->firstOrFail();

        $warehouse->update([
            'akt_number' => $data->aktNumber,
            'akt_date'   => $data->aktDate,
        ]);

        return $warehouse->fresh([
            'items.informationItem.unit',
            'items.type',
            'items.category',
            'items.model',
            'location',
            'assignee',
            'information',
        ]);
    }
}
