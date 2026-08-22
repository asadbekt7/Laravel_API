<?php

declare(strict_types=1);

namespace App\DTOs\Receiving;

use App\Support\NumberToWords\SumToWordsConverter;
use Illuminate\Support\Collection;

/**
 * Bitta akt_number'ga tegishli barcha qatorlarni birlashtirib,
 * "Приёмный акт" blankiga mos header + items ko'rinishida taqdim etadi.
 *
 * Bu Eloquent model emas — chunki bazada "bitta akt" degan alohida jadval yo'q,
 * u information jadvalidagi bir nechta qatorning guruhi sifatida mavjud.
 * Shu sabab alohida o'qish-modeli (read model / DTO) sifatida shakllantiriladi.
 */
final readonly class ReceivingActData
{
    /**
     * @param Collection<int, ReceivingActItemData> $items
     * @param Collection<int, ReceivingApprovalData> $approvals Tasdiqlash holati jadvali
     */
    public function __construct(
        public string $aktNumber,
        public ?string $aktDate,
        public string $hisobFaktura,
        public ?string $hisobFakturaDate,
        public string $contractNumber,
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

    /**
     * @param Collection<int, \App\Models\InformationModel> $rows Bitta akt_number'ga tegishli barcha qatorlar
     */
    public static function fromRows(Collection $rows): self
    {
        /** @var \App\Models\InformationModel $first */
        $first = $rows->first();

        $items = $rows->values()
            ->map(fn ($row, int $index) => ReceivingActItemData::fromModel($row, $index + 1));

        $totalSum = $items->reduce(
            fn (string $carry, ReceivingActItemData $item) => bcadd($carry, $item->sum, 2),
            '0.00'
        );

        return new self(
            aktNumber: $first->akt_number,
            aktDate: $first->akt_date?->format('d.m.Y'),
            hisobFaktura: $first->hisob_faktura,
            hisobFakturaDate: $first->hisob_faktura_date?->format('d.m.Y'),
            contractNumber: $first->contract_number,
            contractDate: $first->contract_date?->format('d.m.Y'),
            supplierName: $first->supplier?->name ?? '',
            ishonchnomaNumber: $first->ishonchnoma_number,
            ishonchnomaDate: $first->ishonchnoma_date?->format('d.m.Y'),
            assigneeName: $first->assignee?->name,
            items: $items,
            totalSum: $totalSum,
            totalSumInWords: SumToWordsConverter::convert($totalSum),
            approvals: self::approvalsFromRows($rows),
        );
    }

    /**
     *
     * @param Collection<int, \App\Models\InformationModel> $rows
     * @return Collection<int, ReceivingApprovalData>
     */
    private static function approvalsFromRows(Collection $rows): Collection
    {
        /** @var \App\Models\InformationModel $first */
        $first = $rows->first();

        $date = fn (?\DateTimeInterface $moment) => $moment?->format('d.m.Y');
        $time = fn (?\DateTimeInterface $moment) => $moment?->format('H:i');

        $approvals = collect();

        if ($creator = $first->creator) {
            $sentAt = $rows->min('created_at');

            $approvals->push(new ReceivingApprovalData(
                rowNumber: $approvals->count() + 1,
                fullName: $creator->full_name ?: $creator->name,
                position: null,
                statusKey: 'sent',
                date: $date($sentAt),
                time: $time($sentAt),
            ));
        }

        if ($assignee = $first->assignee) {
            $acceptedAt = $rows->max('accepted_at');

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
