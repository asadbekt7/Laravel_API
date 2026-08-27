<?php

declare(strict_types=1);

namespace App\Actions\Warehouse;

use App\DTOs\Warehouse\WarehouseAcceptData;
use App\Enums\InformationStatus;
use App\Enums\WarehouseAktStatus;
use App\Models\InformationModel;
use App\Models\WarehouseModel;
use RuntimeException;

final readonly class AcceptInformationToWarehouseAction
{
    public function execute(InformationModel $information, WarehouseAcceptData $data): WarehouseModel
    {
        if (! $information->status->canTransitionTo(InformationStatus::Accepted)) {
            throw new RuntimeException("Bu ma'lumotni qabul qilib bo'lmaydi - u allaqachon ko'rib chiqilgan.");
        }

        $warehouse = WarehouseModel::create([
            'information_id' => $information->id,
            'location_id'    => $data->locationId,
            'description'    => $data->description,
            'assignee_id'    => auth()->id(),
            'akt_number'     => $data->aktNumber,
            'akt_date'       => $data->aktDate,
            // Kirim (qabul qilish) jarayonida status har doim DEFAULT ACCEPTED bo'ladi.
            'status' => WarehouseAktStatus::ACCEPTED,
        ]);

        foreach ($data->items as $item) {
            $warehouse->items()->create([
                'information_item_id' => $item->informationItemId,
                'type_id'             => $item->typeId,
                'category_id'         => $item->categoryId,
                'model_id'            => $item->modelId,
            ]);
        }

        $information->transitionTo(InformationStatus::Accepted);

        return $warehouse->load('items.informationItem.unit', 'location', 'assignee', 'information');
    }
}
