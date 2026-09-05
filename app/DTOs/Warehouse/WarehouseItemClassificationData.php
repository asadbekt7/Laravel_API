<?php

declare(strict_types=1);

namespace App\DTOs\Warehouse;

final readonly class WarehouseItemClassificationData
{
    public function __construct(
        public int $informationItemId,
        // asset_type = 'tmz' bo'lganda typeId/categoryId/modelId umuman
        // kelmaydi, shuning uchun endi nullable.
        public ?int $typeId,
        public ?int $categoryId,
        public ?int $modelId,
        public string $assetType,
        public string $responsiblePersonId,
        public ?string $responsiblePersonName,
        // asset_type = 'asosiy' bo'lganda tmzId umuman kelmaydi.
        public ?int $tmzId,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            informationItemId: (int) $data['information_item_id'],
            typeId: isset($data['type_id']) ? (int) $data['type_id'] : null,
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            modelId: isset($data['model_id']) ? (int) $data['model_id'] : null,
            assetType: (string) $data['asset_type'],
            responsiblePersonId: (string) $data['responsible_person_id'],
            responsiblePersonName: isset($data['responsible_person_name'])
                ? (string) $data['responsible_person_name']
                : null,
            tmzId: isset($data['tmz_id']) ? (int) $data['tmz_id'] : null,
        );
    }
}
