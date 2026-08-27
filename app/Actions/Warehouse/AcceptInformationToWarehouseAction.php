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
        // MUHIM: qatorni qayta, lockForUpdate() bilan o'qiymiz - shu orqali
        // parallel accept/reject so'rovlari bir-birini "ko'rmasdan" o'tib
        // ketishining (race condition) oldi olinadi. Bu faqat DB::transaction()
        // ichida ishlaydi (WarehouseAcceptanceService::accept() shunday chaqiradi).
        /** @var InformationModel $information */
        $information = InformationModel::query()
            ->whereKey($information->id)
            ->lockForUpdate()
            ->firstOrFail();

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
            'status'         => WarehouseAktStatus::ACCEPTED,
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
