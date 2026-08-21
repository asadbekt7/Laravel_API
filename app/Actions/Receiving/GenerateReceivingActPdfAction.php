<?php

declare(strict_types=1);

namespace App\Actions\Receiving;

use App\DTOs\Receiving\ReceivingActData;
use App\Services\PdfService;
use Illuminate\Contracts\Support\Responsable;

/**
 * Bitta vazifa — Приёмный акт blankini PDF ko'rinishida shakllantirish.
 * Controller'dan ajratilgan, chunki PDF generatsiyasi alohida test qilinishi
 * va kelajakda (masalan) Job orqali asinxron chiqarilishi mumkin.
 */
final class GenerateReceivingActPdfAction
{
    public function __construct(
        private readonly PdfService $pdf,
    ) {
    }

    public function execute(ReceivingActData $act, bool $forceDownload = false, string $lang = 'ru'): Responsable
    {
        $lang = in_array($lang, ['uz', 'ru', 'en'], true) ? $lang : 'ru';

        $fileName = sprintf('akt-%s.pdf', str_replace(['/', '\\'], '-', $act->aktNumber));

        $pdf = $this->pdf->fromView('pdf.receiving-act', ['act' => $act, 'lang' => $lang], $fileName);

        return $forceDownload ? $pdf->download($fileName) : $pdf;
    }
}
