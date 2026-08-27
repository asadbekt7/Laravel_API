<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Support\StoredFile;

/**
 * Header (information) ma'lumotlari. Fayl propertylari nullable — chunki
 * update paytida foydalanuvchi faylni almashtirmasligi mumkin (eski fayl saqlanadi).
 */
final readonly class InformationData
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?string $contractNumber = null,
        public ?string $contractDate = null,
        public ?StoredFile $contractFile = null,
        public ?int $supplierId = null,
        public ?string $bildirishnomaNumber = null,
        public ?string $bildirishnomaDate = null,
        public ?StoredFile $bildirishnomaFile = null,
        public ?string $ishonchnomaNumber = null,
        public ?string $ishonchnomaDate = null,
        public ?StoredFile $ishonchnomaFile = null,
        public ?string $hisobFaktura = null,
        public ?string $hisobFakturaDate = null,
        public ?StoredFile $hisobFakturaFile = null,
    ) {
    }

    /**
     * Faqat qiymati bor (null bo'lmagan) maydonlarni massivga o'giradi —
     * shu bilan bir DTO ham create, ham partial update uchun ishlaydi.
     */
    public function toArray(): array
    {
        $map = [
            'name'                 => $this->name,
            'description'          => $this->description,
            'contract_number'      => $this->contractNumber,
            'contract_date'        => $this->contractDate,
            'supplier_id'          => $this->supplierId,
            'bildirishnoma_number' => $this->bildirishnomaNumber,
            'bildirishnoma_date'   => $this->bildirishnomaDate,
            'ishonchnoma_number'   => $this->ishonchnomaNumber,
            'ishonchnoma_date'     => $this->ishonchnomaDate,
            'hisob_faktura'        => $this->hisobFaktura,
            'hisob_faktura_date'   => $this->hisobFakturaDate,
        ];

        foreach (['contract', 'bildirishnoma', 'ishonchnoma', 'hisob_faktura'] as $prefix) {
            $property = lcfirst(str_replace('_', '', ucwords($prefix, '_'))) . 'File';
            /** @var ?StoredFile $file */
            $file = $this->{$property};

            if ($file !== null) {
                $map["{$prefix}_file_path"] = $file->path;
                $map["{$prefix}_file_name"] = $file->name;
            }
        }

        return array_filter($map, fn ($value) => $value !== null);
    }
}
