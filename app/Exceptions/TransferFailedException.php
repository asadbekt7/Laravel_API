<?php

namespace App\Exceptions;

use Exception;

class TransferFailedException extends Exception
{
    public function __construct(
        string $message = 'Ko\'chirish jarayonida xatolik yuz berdi. Barcha o\'zgarishlar bekor qilindi.',
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
    public static function fromPrevious(\Throwable $previous): static
    {
        return new static(
            'Ko\'chirish tranzaksiyasi muvaffaqiyatsiz tugadi: ' . $previous->getMessage(),
            500,
            $previous
        );
    }
}
