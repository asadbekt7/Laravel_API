<?php

declare(strict_types=1);

namespace App\Services\FileStorage;

use App\Support\StoredFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class InformationFileUploader implements InformationFileUploaderInterface
{
    /**
     * DIQQAT (xavfsizlik): shartnoma kabi hujjatlar odatda maxfiy bo'ladi.
     * Production'da 'public' disk o'rniga 'local'/'s3' (private) diskdan
     * va signed URL orqali yuklab olishni tavsiya qilaman — pastga qarang.
     */
    private const string DISK = 'public';

    public function store(UploadedFile $file, string $folder): StoredFile
    {
        $path = $file->store("informations/{$folder}", self::DISK);

        return new StoredFile($path, $file->getClientOriginalName());
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }
}
