<?php

namespace App\Services\WarehouseBatch;

use App\Models\WarehouseBatch;
use App\Services\PdfService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BatchPdfService
{
    public function __construct(private readonly PdfService $pdf) {}

    public function generate(WarehouseBatch $batch): void
    {
        $batchId = $batch->id;

        DB::afterCommit(function () use ($batchId) {
            $batch = WarehouseBatch::with([
                'items.warehouse.unit',
                'signers.user',
                'staff',
                'createdBy',
            ])->find($batchId);

            if (! $batch) {
                return;
            }

            try {
                $path = $this->pdf->saveToDisk(
                    view: 'yuk-xati',
                    data: ['batch' => $batch],
                    path: "batches/{$batch->id}.pdf",
                );

                $batch->update(['pdf_path' => $path]);
            } catch (\Throwable $e) {
                Log::error('Batch PDF generatsiya xatosi ['.$batch->batch_number.']: '.$e->getMessage());
            }
        });
    }
}
