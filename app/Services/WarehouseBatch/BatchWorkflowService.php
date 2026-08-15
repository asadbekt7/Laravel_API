<?php
// app/Services/WarehouseBatch/BatchWorkflowService.php

namespace App\Services\WarehouseBatch;

use App\Enums\BatchStatus;
use App\Enums\SignerLevelStatus;
use App\Events\WarehouseBatchCompleted;
use App\Models\WarehouseBatch;

class BatchWorkflowService
{
    public function advance(WarehouseBatch $batch, int $completedLevel): void
    {
        $next = $batch->signers()->where('level', $completedLevel + 1)->first();

        if ($next) {
            $next->update(['status' => SignerLevelStatus::Active]);

            return;
        }

        $batch->update([
            'status' => BatchStatus::Completed,
            'completed_at' => now(),
        ]);

        event(new WarehouseBatchCompleted($batch));
    }

    public function reject(WarehouseBatch $batch): void
    {
        $batch->signers()
            ->where('status', SignerLevelStatus::Pending)
            ->update(['status' => SignerLevelStatus::Rejected]);

        $batch->update([
            'status' => BatchStatus::Rejected,
            'completed_at' => now(),
        ]);
    }
}
