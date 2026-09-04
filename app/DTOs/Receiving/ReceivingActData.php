<?php

declare(strict_types=1);

namespace App\DTOs\Receiving;

use App\Models\WarehouseModel;
use App\Support\NumberToWords\SumToWordsConverter;
use Illuminate\Support\Collection;

/**
 * Bitta qabul aktini "Приёмный акт" blankiga mos header + items ko'rinishida
 * taqdim etadi.
 */
final readonly class ReceivingActData
{
    /**
     * @param Collection<int, ReceivingActItemData> $items
     * @param Collection<int, ReceivingApprovalData> $approvals Tasdiqlash holati jadvali
     */
    public function __construct(
        public int $warehouseId,
        public ?string $aktNumber,
        public ?string $aktDate,
        public ?string $hisobFaktura,
        public ?string $hisobFakturaDate,
        public ?string $contractNumber,
        public ?string $contractDate,
        public string $supplierName,
        public ?string $ishonchnomaNumber,
        public ?string $ishonchnomaDate,
        public ?string $assigneeName,
        public Collection $items,
        public string $totalSum,
        public string $totalSumInWords,
        public Collection $approvals,
    ) {
    }

    public static function fromWarehouse(WarehouseModel $warehouse): self
    {
        $information = $warehouse->information;

        $items = $warehouse->items
            ->values()
            ->map(fn ($warehouseItem, int $index) => ReceivingActItemData::fromModel(
                $warehouseItem->informationItem,
                $index + 1,
            ));

        $totalSum = $items->reduce(
            fn (string $carry, ReceivingActItemData $item) => bcadd($carry, $item->sum, 2),
            '0.00'
        );

        return new self(
            warehouseId: $warehouse->id,
            aktNumber: (string) $warehouse->akt_number,
            aktDate: $warehouse->akt_date?->format('d.m.Y'),
            hisobFaktura: $information?->hisob_faktura,
            hisobFakturaDate: $information?->hisob_faktura_date?->format('d.m.Y'),
            contractNumber: $information?->contract_number,
            contractDate: $information?->contract_date?->format('d.m.Y'),
            supplierName: $information?->supplier?->name ?? '',
            ishonchnomaNumber: $information?->ishonchnoma_number,
            ishonchnomaDate: $information?->ishonchnoma_date?->format('d.m.Y'),
            assigneeName: $warehouse->assignee?->full_name ?: $warehouse->assignee?->name,
            items: $items,
            totalSum: $totalSum,
            totalSumInWords: SumToWordsConverter::convert($totalSum),
            approvals: self::approvalsFrom($warehouse),
        );
    }

    /**
     * @return Collection<int, ReceivingApprovalData>
     */
    private static function approvalsFrom(WarehouseModel $warehouse): Collection
    {
        $date = fn (?\DateTimeInterface $moment) => $moment?->format('d.m.Y');
        $time = fn (?\DateTimeInterface $moment) => $moment?->format('H:i');

        $approvals = collect();

        if ($creator = $warehouse->information?->creator) {
            $sentAt = $warehouse->information->created_at;

            $approvals->push(new ReceivingApprovalData(
                rowNumber: $approvals->count() + 1,
                fullName: $creator->full_name ?: $creator->name,
                position: null,
                statusKey: 'sent',
                date: $date($sentAt),
                time: $time($sentAt),
            ));
        }

        if ($assignee = $warehouse->assignee) {
            $acceptedAt = $warehouse->information?->accepted_at ?? $warehouse->created_at;

            $approvals->push(new ReceivingApprovalData(
                rowNumber: $approvals->count() + 1,
                fullName: $assignee->full_name ?: $assignee->name,
                position: null,
                statusKey: 'received',
                date: $date($acceptedAt),
                time: $time($acceptedAt),
            ));
        }

        return $approvals;
    }
}
