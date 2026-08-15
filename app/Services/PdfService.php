<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
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

    /**
     * PDF'ni diskka saqlaydi va disk ichidagi nisbiy yo'lni qaytaradi.
     * Standart 'local' disk (storage/app/private) — Storage::disk('local') orqali o'qiladi.
     */
    public function saveToDisk(
        string $view,
        array $data,
        string $path,
        string $disk = 'local',
        string $format = 'a4',
    ): string {
        $storage = Storage::disk($disk);
        $storage->makeDirectory(dirname($path)); // papka bo'lmasa yaratamiz

        Pdf::view($view, $data)
            ->format($format)
            ->save($storage->path($path));

        return $path;
    }

    private function normalizeName(string $filename): string
    {
        return str_ends_with(strtolower($filename), '.pdf')
            ? $filename
            : $filename.'.pdf';
    }
}
