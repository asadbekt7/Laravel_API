<?php
// app/Services/WarehouseBatch/WarehouseBatchSignService.php

namespace App\Services\WarehouseBatch;

use App\Enums\SignerLevelStatus;
use App\Models\BugalteriyaModel;
use App\Models\User;
use App\Models\WarehouseBatch;
use DomainException;
use Illuminate\Support\Facades\DB;

class WarehouseBatchSignService
{
    public function __construct(
        private readonly BatchWorkflowService $workflowService,
    ) {}

    /** MENYU 2 — buxgalter debit/kredit/talab_qilingan to'ldirib, 1-darajani tasdiqlaydi.
     *
     * @param array $entries [['id'=>int,'debit'=>string,'kredit'=>string,'talab_qilingan'=>int], ...]
     */
    public function signAsAccountant(WarehouseBatch $batch, User $accountant, array $entries): WarehouseBatch
    {
        return DB::transaction(function () use ($batch, $accountant, $entries) {
            $batch = WarehouseBatch::whereKey($batch->id)->lockForUpdate()->firstOrFail();

            $active = $batch->signers()
                ->where('level', 1)
                ->where('status', SignerLevelStatus::Active)
                ->where('user_id', $accountant->id)
                ->first();

            if (! $active) {
                throw new DomainException('Sizning navbatingiz emas yoki batch yopilgan.');
            }

            $rows = $batch->entries;

            // === BUG FIX ===
            // ESKI shart edi: "har bir yozuv status'i COMPLETED bo'lishi kerak". Bu shart
            // CANCELLED (Menyu 1'da bekor qilingan) yozuv bo'lsa, HECH QACHON bajarilmasdi —
            // chunki CANCELLED hech qachon COMPLETED'ga aylanmaydi — va batch abadiy
            // Menyu 2'ga o'ta olmay muzlab qolardi.
            //
            // TO'G'RI shart: faqat PENDING (hali tasniflanmagan) qolmasligi kerak.
            // COMPLETED va CANCELLED — ikkalasi ham "hal qilingan" hisoblanadi.
            if ($rows->contains(fn ($r) => $r->status === BugalteriyaModel::STATUS_PENDING)) {
                throw new DomainException('Avval barcha mahsulotlar tasniflanishi (Menyu 1) kerak.');
            }

            // Debit/kredit faqat COMPLETED (ya'ni items jadvaliga o'tgan) yozuvlarga tegishli —
            // CANCELLED yozuvlar bu ro'yxatda umuman bo'lmasligi kerak.
            $completedIds = $rows->where('status', BugalteriyaModel::STATUS_COMPLETED)->pluck('id');
            $submittedIds = collect($entries)->pluck('id');

            // Ikki tomonlama tekshiruv: na kam, na ortiq bo'lmasligi kerak — aks holda
            // bugalter ba'zi yozuvlarni "unutib" ham imzolab yuborishi mumkin edi.
            if ($completedIds->diff($submittedIds)->isNotEmpty() || $submittedIds->diff($completedIds)->isNotEmpty()) {
                throw new DomainException('Barcha tasniflangan (COMPLETED) mahsulotlar uchun debit/kredit/talab qilingan to\'ldirilishi shart.');
            }

            foreach ($entries as $entry) {
                BugalteriyaModel::where('id', $entry['id'])
                    ->where('batch_id', $batch->id)
                    ->update([
                        'debit' => $entry['debit'],
                        'kredit' => $entry['kredit'],
                        'talab_qilingan' => $entry['talab_qilingan'],
                    ]);
            }

            $active->update([
                'status' => SignerLevelStatus::Approved,
                'comment' => 'Ma\'lumotlar to\'ldirildi',
                'responded_at' => now(),
            ]);

            $this->workflowService->advance($batch, completedLevel: 1);

            return $batch->fresh(['entries', 'signers.user']);
        });
    }
}
