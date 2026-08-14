<?php
// app/Services/WarehouseBatch/WarehouseBatchApprovalService.php
namespace App\Services\WarehouseBatch;

use App\Enums\SignerLevelStatus;
use App\Exceptions\BatchSignException;
use App\Models\User;
use App\Models\WarehouseBatch;
use App\Models\WarehouseBatchSigner;
use Illuminate\Support\Facades\DB;

class WarehouseBatchApprovalService
{
    public function __construct(
        private readonly BatchWorkflowService $workflowService,
    ) {}

    public function approve(WarehouseBatch $batch, User $user, ?string $comment): WarehouseBatch
    {
        return DB::transaction(function () use ($batch, $user, $comment) {
            $active = $this->lockActiveSigner($batch, $user);

            $active->update([
                'status' => SignerLevelStatus::Approved,
                'comment' => $comment,
                'responded_at' => now(),
            ]);

            $this->workflowService->advance($active->batch, completedLevel: $active->level);

            return $batch->fresh(['items.warehouse', 'signers.user']);
        });
    }

    public function reject(WarehouseBatch $batch, User $user, string $comment): WarehouseBatch
    {
        return DB::transaction(function () use ($batch, $user, $comment) {
            $active = $this->lockActiveSigner($batch, $user);

            $active->update([
                'status' => SignerLevelStatus::Rejected,
                'comment' => $comment,
                'responded_at' => now(),
            ]);

            $this->workflowService->reject($active->batch);

            return $batch->fresh(['items.warehouse', 'signers.user']);
        });
    }

    private function lockActiveSigner(WarehouseBatch $batch, User $user): WarehouseBatchSigner
    {
        $batch = WarehouseBatch::whereKey($batch->id)->lockForUpdate()->firstOrFail();

        $active = $batch->signers()
            ->where('status', SignerLevelStatus::Active)
            ->where('user_id', $user->id)
            ->first();

        if (! $active) {
            throw new BatchSignException('Sizning navbatingiz emas yoki batch yopilgan.');
        }

        return $active;
    }
}
