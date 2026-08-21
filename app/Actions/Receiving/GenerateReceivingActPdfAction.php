<?php

declare(strict_types=1);

namespace App\Actions\Receiving;

use App\DTO\Receiving\ReceivingActData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

/**
 * Bitta vazifa — Приёмный акт blankini PDF ko'rinishida shakllantirish.
 * Controller'dan ajratilgan, chunki PDF generatsiyasi alohida test qilinishi
 * va kelajakda (masalan) Job orqali asinxron chiqarilishi mumkin.
 */
final class GenerateReceivingActPdfAction
{
    public function execute(ReceivingActData $act, bool $forceDownload = false): Response
    {
        $pdf = Pdf::loadView('pdf.receiving-act', ['act' => $act])
            ->setPaper('a4', 'portrait');

        $fileName = sprintf('akt-%s.pdf', str_replace(['/', '\\'], '-', $act->aktNumber));

        return $forceDownload
            ? $pdf->download($fileName)
            : $pdf->stream($fileName);
    }
}
