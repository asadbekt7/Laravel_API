<?php
// app/DTOs/CreateWarehouseBatchDTO.php
namespace App\DTOs;

final class CreateWarehouseBatchDTO
{
    /**
     * @param array{warehouse_id:int,quantity:int}[] $products
     * @param int[] $signerIds  Tartibda: [0]=buxgalter (1-daraja), [1..]=keyingi tasdiqlovchilar
     */
    public function __construct(
        public readonly int $staffId,
        public readonly int $createdBy,
        public readonly array $products,
        public readonly array $signerIds,
    ) {}

    public static function fromRequest(array $data, int $createdBy): self
    {
        return new self(
            staffId: $data['staff_id'],
            createdBy: $createdBy,
            products: $data['products'],
            signerIds: $data['signer_ids'],
        );
    }
}
