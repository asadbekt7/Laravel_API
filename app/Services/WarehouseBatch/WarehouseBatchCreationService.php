<?php

namespace App\Services\WarehouseBatch;

use App\Enums\BatchStatus;
use App\Enums\SignerLevelStatus;
use App\Models\User;
use App\Models\WarehouseBatch;
use Illuminate\Support\Facades\DB;

class WarehouseBatchCreationService
{
    public function __construct(private readonly BatchPdfService $pdf) {}

    /** @param array<int, array{staff_id: int, full_name: string}> $signers Tartibda: [0]=buxgalter (1-daraja) */
    public function create(string $batchNumber, int $createdBy, array $signers): WarehouseBatch
    {
        $batch = DB::transaction(function () use ($batchNumber, $createdBy, $signers) {
            $batch = WarehouseBatch::create([
                'batch_number' => $batchNumber,
                'created_by' => $createdBy,
                'status' => BatchStatus::InProgress,
            ]);

            foreach (array_values($signers) as $index => $signer) {
                $level = $index + 1;
                $user = User::resolveFromStaff($signer['staff_id'], $signer['full_name']);

                $batch->signers()->create([
                    'user_id' => $user->id,
                    'level' => $level,
                    'status' => $level === 1 ? SignerLevelStatus::Active : SignerLevelStatus::Pending,
                ]);
            }

            return $batch->load('signers.user');
        });

        $this->pdf->generate($batch);

        return $batch;
    }
}
