<?php

declare(strict_types=1);

namespace App\Enums;

enum InformationStatus: string
{
    case Pending    = 'pending';
    case Accepted   = 'accepted';
    case Rejected   = 'rejected';
    case InProgress = 'in_progress';
    case Completed  = 'completed';

    /**
     * Ruxsat etilgan status o'tishlari (oddiy state machine).
     * Key - joriy status, Value - o'sha statusdan borish mumkin bo'lgan statuslar ro'yxati.
     */
    private const TRANSITIONS = [
        self::Pending->value    => [self::Accepted, self::Rejected],
        self::Accepted->value   => [self::InProgress],
        // Rejected'dan Pending'ga qaytish - foydalanuvchi ma'lumotni
        // to'g'irlab qayta yuborganda (resubmit) ishlatiladi.
        self::Rejected->value   => [self::Pending],
        self::InProgress->value => [self::Completed],
        self::Completed->value  => [],
    ];

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, self::TRANSITIONS[$this->value], true);
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'Kutilmoqda',
            self::Accepted   => 'Qabul qilingan',
            self::Rejected   => 'Rad etilgan',
            self::InProgress => 'Jarayonda',
            self::Completed  => 'Yakunlangan',
        };
    }
}
