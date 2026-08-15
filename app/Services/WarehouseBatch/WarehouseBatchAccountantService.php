<?php
// app/Services/WarehouseBatch/WarehouseBatchAccountantService.php
namespace App\Services\WarehouseBatch;

use App\Enums\SignerLevelStatus;
use App\Exceptions\BatchSignException;
use App\Models\User;
use App\Models\WarehouseBatch;
use App\Models\WarehouseBatchItem;
use Illuminate\Support\Facades\DB;

class WarehouseBatchAccountantService
{
    public function __construct(
        private readonly BatchWorkflowService $workflowService,
        private readonly BatchPdfService $pdf,
    ) {}

    /** @param array $entries [['id'=>int,'debit'=>string,'kredit'=>string,'talab_qilingan'=>int], ...] */
    public function accept(WarehouseBatch $batch, User $accountant, array $entries): WarehouseBatch
    {
        $batch = DB::transaction(function () use ($batch, $accountant, $entries) {
            $batch = WarehouseBatch::whereKey($batch->id)->lockForUpdate()->firstOrFail();

            $active = $batch->signers()
                ->where('level', 1)
                ->where('status', SignerLevelStatus::Active)
                ->where('user_id', $accountant->id)
                ->first();

            if (! $active) {
                throw new BatchSignException('Sizning navbatingiz emas yoki batch yopilgan.');
            }

            $itemIds = $batch->items()->pluck('id');
            $submittedIds = collect($entries)->pluck('id');

            if ($itemIds->diff($submittedIds)->isNotEmpty()) {
                throw new BatchSignException('Barcha mahsulotlar uchun debit/kredit/talab qilingan to\'ldirilishi shart.');
            }

            foreach ($entries as $entry) {
                WarehouseBatchItem::where('id', $entry['id'])
                    ->where('batch_id', $batch->id) // IDOR himoyasi
                    ->update([
                        'debit' => $entry['debit'],
                        'kredit' => $entry['kredit'],
                        'talab_qilingan' => $entry['talab_qilingan'],
                    ]);
            }

            $active->update([
                'status' => SignerLevelStatus::Approved,
                'comment' => 'Qabul qilindi',
                'responded_at' => now(),
            ]);

            $this->workflowService->advance($batch, completedLevel: 1);

            return $batch->fresh(['items.warehouse', 'signers.user']);
        });

        // Debit/kredit/talab to'ldirilgach — hujjatni qayta generatsiya.
        $this->pdf->generate($batch);

        return $batch;
    }
}
