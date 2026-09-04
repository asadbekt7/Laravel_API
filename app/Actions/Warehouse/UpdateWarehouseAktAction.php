<?php

declare(strict_types=1);

namespace App\Actions\Warehouse;

use App\DTOs\Warehouse\WarehouseUpdateData;
use App\Models\WarehouseModel;

final readonly class UpdateWarehouseAktAction
{
    public function execute(WarehouseModel $warehouse, WarehouseUpdateData $data): WarehouseModel
    {
        /** @var WarehouseModel $warehouse */
        $warehouse = WarehouseModel::query()
            ->whereKey($warehouse->id)
            ->lockForUpdate()
            ->firstOrFail();

        $oldAktNumber = (int) $warehouse->akt_number;
        $newAktNumber = $data->aktNumber;

        if ($oldAktNumber !== $newAktNumber) {
            $this->reorderIfNeeded($warehouse, $oldAktNumber, $newAktNumber);
        }

        $warehouse->update([
            'akt_number' => $newAktNumber,
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

    /**
     * Agar yangi akt_number allaqachon boshqa aktda band bo'lsa - oraliqdagi
     * barcha aktlarni bittadan surib, "joy bo'shatadi". Faqat ikkalasi ham
     * sof raqam (masalan "1", "20") bo'lgandagina ishlaydi - "257-A" kabi
     * aralash formatlarga tegmaydi.
     */
    private function reorderIfNeeded(WarehouseModel $warehouse, int $oldAktNumber, int $newAktNumber): void
    {
        $conflict = WarehouseModel::query()
            ->where('akt_number', $newAktNumber)
            ->where('id', '!=', $warehouse->id)
            ->lockForUpdate()
            ->exists();

        if (! $conflict) {
            return; // yangi raqam bo'sh - siljitishning hojati yo'q
        }

        // Ta'sirlanadigan oraliq va yo'nalish: pastga ko'chirilsa (20->10) -
        // [10,19] oralig'i +1'ga suriladi; yuqoriga ko'chirilsa (5->15) -
        // [6,15] oralig'i -1'ga suriladi.
        [$from, $to, $step] = $newAktNumber < $oldAktNumber
            ? [$newAktNumber, $oldAktNumber - 1, 1]
            : [$oldAktNumber + 1, $newAktNumber, -1];

        $affected = WarehouseModel::query()
            ->whereBetween('akt_number', [$from, $to])
            ->where('id', '!=', $warehouse->id)
            ->lockForUpdate()
            ->get(['id', 'akt_number']);

        // Har bir qatorning HAQIQIY (surishdan oldingi) raqamini eslab qolamiz -
        // chunki update() dan keyin akt_number allaqachon o'zgargan bo'ladi.
        $originalNumbers = $affected->pluck('akt_number', 'id');

        // 1-bosqich: barchasini vaqtinchalik, hech kim bilan to'qnashmaydigan
        // qiymatga o'tkazamiz (id noyob bo'lgani uchun tmp_{id} hech qachon
        // boshqa qator bilan to'qnashmaydi).
        foreach ($affected as $row) {
            $row->update(['akt_number' => -$row->id]);
        }

        // 2-bosqich: yakuniy (surilgan) raqamlarni yozamiz.
        foreach ($affected as $row) {
            $row->update(['akt_number' => (int) $originalNumbers[$row->id] + $step]);
        }
    }
}
