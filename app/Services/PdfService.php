<?php

namespace App\Services;

use Spatie\LaravelPdf\PdfBuilder;
use Spatie\LaravelPdf\Facades\Pdf;

class PdfService
{
    public function fromView(
        string $view,
        array $data = [],
        string $filename = 'document.pdf',
        string $format = 'a4',
    ): PdfBuilder {
        return Pdf::view($view, $data)
            ->format($format)
            ->name($this->normalizeName($filename));
    }

    public function saveToDisk(
        string $view,
        array $data,
        string $path,
        string $format = 'a4',
    ): string {
        Pdf::view($view, $data)
            ->format($format)
            ->save(storage_path('app/'.$path));

        return $path;
    }

    private function normalizeName(string $filename): string
    {
        return str_ends_with(strtolower($filename), '.pdf')
            ? $filename
            : $filename.'.pdf';
    }
}
