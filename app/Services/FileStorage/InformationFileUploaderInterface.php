<?php

declare(strict_types=1);

namespace App\Services\FileStorage;

use App\Support\StoredFile;
use Illuminate\Http\UploadedFile;

interface InformationFileUploaderInterface
{
    public function store(UploadedFile $file, string $folder): StoredFile;

    public function delete(?string $path): void;
}
