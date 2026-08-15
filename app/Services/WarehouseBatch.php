<?php
// app/Services/WarehouseBatch/WarehouseBatchCreationService.php

namespace App\Services\WarehouseBatch;

use App\Enums\BatchStatus;
use App\Enums\SignerLevelStatus;
use App\Models\WarehouseBatch;
use Illuminate\Support\Facades\DB;

class WarehouseBatchCreationService
{
    /** @param int[] $signerIds Tartibda: [0]=buxgalter (1-daraja), [1..]=keyingi tasdiqlovchilar */
    public function create(string $batchNumber, int $createdBy, array $signerIds): WarehouseBatch
    {
        return DB::transaction(function () use ($batchNumber, $createdBy, $signerIds) {
            $batch = WarehouseBatch::create([
                'batch_number' => $batchNumber,
                'created_by' => $createdBy,
                'status' => BatchStatus::InProgress,
            ]);

            foreach (array_values($signerIds) as $index => $userId) {
                $level = $index + 1;

                $batch->signers()->create([
                    'user_id' => $userId,
                    'level' => $level,
                    'status' => $level === 1 ? SignerLevelStatus::Active : SignerLevelStatus::Pending,
                ]);
            }

            return $batch->load('signers.user');
        });
    }
}
