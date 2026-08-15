<?php
// app/Services/WarehouseBatch/WarehouseBatchApprovalService.php

namespace App\Services\WarehouseBatch;

use App\Enums\SignerLevelStatus;
use App\Models\User;
use App\Models\WarehouseBatch;
use App\Models\WarehouseBatchSigner;
use DomainException;
use Illuminate\Support\Facades\DB;

class WarehouseBatchApprovalService
{
    public function __construct(
        private readonly BatchWorkflowService $workflowService,
    ) {}

    public function approve(WarehouseBatch $batch, User $user, ?string $comment): WarehouseBatch
    {
        return DB::transaction(function () use ($batch, $user, $comment) {
            [$lockedBatch, $active] = $this->lockActiveSigner($batch, $user);

            $active->update([
                'status' => SignerLevelStatus::Approved,
                'comment' => $comment,
                'responded_at' => now(),
            ]);

            $this->workflowService->advance($lockedBatch, completedLevel: $active->level);

            return $lockedBatch->fresh(['entries', 'signers.user']);
        });
    }

    public function reject(WarehouseBatch $batch, User $user, string $comment): WarehouseBatch
    {
        return DB::transaction(function () use ($batch, $user, $comment) {
            [$lockedBatch, $active] = $this->lockActiveSigner($batch, $user);

            $active->update([
                'status' => SignerLevelStatus::Rejected,
                'comment' => $comment,
                'responded_at' => now(),
            ]);

            $this->workflowService->reject($lockedBatch);

            return $lockedBatch->fresh(['entries', 'signers.user']);
        });
    }

    /** @return array{0: WarehouseBatch, 1: WarehouseBatchSigner} */
    private function lockActiveSigner(WarehouseBatch $batch, User $user): array
    {
        $lockedBatch = WarehouseBatch::whereKey($batch->id)->lockForUpdate()->firstOrFail();

        $active = $lockedBatch->signers()
            ->where('status', SignerLevelStatus::Active)
            ->where('user_id', $user->id)
            ->first();

        if (! $active) {
            throw new DomainException('Sizning navbatingiz emas yoki batch yopilgan.');
        }

        return [$lockedBatch, $active];
    }
}
