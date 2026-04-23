<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class RoomApiException extends Exception
{
    public function __construct(
        string $message = 'Room API dan ma\'lumot olishda xatolik yuz berdi.',
        int $code = 503,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
    public static function requestFailed(?Throwable $previous = null): static
    {
        return new static(
            'Tashqi Room API ga ulanib bo\'lmadi yoki so\'rov vaqti tugadi.',
            503,
            $previous
        );
    }
    public static function roomNotFound(string $roomName): static
    {
        return new static(
            "'{$roomName}' nomli xona Room API da topilmadi.",
            404
        );
    }
    public static function invalidResponse(): static
    {
        return new static(
            'Room API kutilmagan formatda javob qaytardi.',
            502
        );
    }

}
