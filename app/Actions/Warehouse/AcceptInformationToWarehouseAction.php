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
            'description'    => $data->description ?? '',
            'assignee_id'    => auth()->id(),
            'akt_number'     => $data->aktNumber,
            'akt_date'       => $data->aktDate,
            'status'         => WarehouseAktStatus::ACCEPTED,
        ]);


        $quantities = $information->items()->pluck('quantity', 'id');

        foreach ($data->items as $item) {
            $warehouse->items()->create([
                'information_item_id' => $item->informationItemId,
                'quantity'            => $quantities[$item->informationItemId] ?? 0,
                'type_id'             => $item->typeId,
                'category_id'         => $item->categoryId,
                'model_id'            => $item->modelId,

                // YANGI:
                'asset_type'              => $item->assetType,
                'responsible_person_id'   => $item->responsiblePersonId,
                'responsible_person_name' => $item->responsiblePersonName,
                'tmz_id'                  => $item->tmzId,
            ]);
        }

        $information->transitionTo(InformationStatus::Accepted);

        // YANGI: 'items.tmz' qo'shildi — shunda accept() javobida ham
        // TMZ turidagi itemlarning tmz ma'lumoti (nomi) darhol ko'rinadi,
        // qo'shimcha so'rov (N+1) bo'lmaydi.
        return $warehouse->load('items.informationItem.unit', 'items.tmz', 'location', 'assignee', 'information');
    }
}
