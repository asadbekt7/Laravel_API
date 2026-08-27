<?php
// app/Actions/InformationItem/AddInformationItemAction.php
declare(strict_types=1);

namespace App\Actions\InformationItem;

use App\DTOs\InformationItemData;
use App\Models\InformationItem;
use App\Models\InformationModel;

final readonly class AddInformationItemAction
{
    public function execute(InformationModel $information, InformationItemData $data): InformationItem
    {
        return $information->items()->create([
            'product_name' => $data->productName,
            'unit_id'      => $data->unitId,
            'quantity'     => $data->quantity,
            'item_price'   => $data->itemPrice,
        ]);
    }
}
