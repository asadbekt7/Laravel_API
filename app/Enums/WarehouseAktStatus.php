<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * DIQQAT - bu enum IKKITA turli jarayon uchun bitta ustunda ishlatiladi:
 *
 *  - ACCEPTED   -> "kirim" (information'dan omborga qabul qilish) jarayonida
 *                  ishlatiladi. `warehouse` qatori yaratilishi bilan
 *                  DEFAULT holatda shu qiymat beriladi (AcceptInformationToWarehouseAction).
 *
 *  - SUBMITTED, REJECTED, COMPLETED -> "chiqim" (ombordan mahsulot chiqarish)
 *                  jarayoni uchun ZAXIRALANGAN. Bu jarayon hozircha kodda
 *                  yozilmagan - kelajakda alohida Action/Service sifatida
 *                  qo'shiladi. Hozirgi accept/reject oqimida bu 3 status
 *                  hech qachon ishlatilmaydi.
 */
enum WarehouseAktStatus: string
{
    case ACCEPTED  = 'accepted';
    case SUBMITTED = 'submitted';
    case REJECTED  = 'rejected';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::ACCEPTED  => 'Qabul qilingan',
            self::SUBMITTED => 'Yuborilgan',
            self::REJECTED  => 'Rad etilgan',
            self::COMPLETED => 'Yakunlangan',
        };
    }
}
