<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Berilgan akt_number bo'yicha (yakunlangan holatdagi) hech qanday
 * information yozuvi topilmaganda otiladi. API-only loyiha bo'lgani uchun
 * to'g'ridan-to'g'ri JSON javob shakllantiradi (render()) — controllerda
 * try/catch shart emas, Laravel exception handler avtomatik chaqiradi.
 */
final class ReceivingActNotFoundException extends Exception
{
    public function __construct(public readonly string $aktNumber)
    {
        parent::__construct("«{$aktNumber}» akt raqamiga tegishli yakunlangan ma'lumot topilmadi.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 404);
    }
}
