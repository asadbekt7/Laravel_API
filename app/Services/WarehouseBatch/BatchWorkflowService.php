<?php
// app/Services/WarehouseBatch/BatchWorkflowService.php
namespace App\Services\WarehouseBatch;

use App\Enums\BatchStatus;
use App\Enums\SignerLevelStatus;
use App\Events\BatchCompleted;
use App\Events\BatchSignerActivated;
use App\Models\WarehouseBatch;

class BatchWorkflowService
{
    /** Joriy daraja tasdiqlangach — keyingisini "active" qiladi yoki batchni yopadi */
    public function advance(WarehouseBatch $batch, int $completedLevel): void
    {
        $next = $batch->signers()->where('level', $completedLevel + 1)->first();

        if ($next) {
            $next->update(['status' => SignerLevelStatus::Active]);
            event(new BatchSignerActivated($next));

            return;
        }

        $batch->update([
            'status' => BatchStatus::Completed,
            'completed_at' => now(),
        ]);

        event(new BatchCompleted($batch));
    }

    /** Rad etilganda — qolgan barcha "pending" darajalar ham rad etilgan deb belgilanadi */
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
