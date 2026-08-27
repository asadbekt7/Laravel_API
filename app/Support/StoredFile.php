<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Saqlangan faylning path va original nomini birga tashuvchi qiymat obyekti.
 */
final readonly class StoredFile
{
    public function __construct(
        public string $path,
        public string $name,
    ) {
    }
}
