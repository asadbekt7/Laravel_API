<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
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
        return $this->applyBrowserOptions(
            Pdf::view($view, $data)
                ->format($format)
                ->name($this->normalizeName($filename))
        );
    }

    public function saveToDisk(
        string $view,
        array $data,
        string $path,
        string $disk = 'local',
        string $format = 'a4',
    ): string {
        $storage = Storage::disk($disk);
        $storage->makeDirectory(dirname($path));

        $this->applyBrowserOptions(
            Pdf::view($view, $data)->format($format)
        )->save($storage->path($path));

        return $path;
    }

    /**
     * Serverda (Linux) Chromium ko'pincha sandbox'ni ishga tushira olmaydi:
     * Ubuntu 23.10+ da unprivileged user namespaces AppArmor orqali yopiq,
     * shu sabab web-server foydalanuvchisi ostida "No usable sandbox" xatosi chiqadi.
     * Lokal (Windows/macOS) muhitga tegmaymiz.
     */
    private function applyBrowserOptions(PdfBuilder $builder): PdfBuilder
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return $builder;
        }

        return $builder->withBrowsershot(
            fn (Browsershot $browsershot) => $browsershot->noSandbox()
        );
    }

    private function normalizeName(string $filename): string
    {
        return str_ends_with(strtolower($filename), '.pdf')
            ? $filename
            : $filename.'.pdf';
    }
}
