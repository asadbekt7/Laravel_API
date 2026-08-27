<?php
// app/Actions/InformationItem/UpdateInformationItemAction.php
declare(strict_types=1);

namespace App\Actions\InformationItem;

use App\DTOs\InformationItemData;
use App\Models\InformationItem;

final readonly class UpdateInformationItemAction
{
    public function execute(InformationItem $item, InformationItemData $data): InformationItem
    {
        $item->update([
            'product_name' => $data->productName,
            'unit_id'      => $data->unitId,
            'quantity'     => $data->quantity,
            'item_price'   => $data->itemPrice,
        ]);

        return $item;
    }
}
