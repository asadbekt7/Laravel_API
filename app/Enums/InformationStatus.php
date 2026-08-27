<?php

declare(strict_types=1);

namespace App\Enums;

enum InformationStatus: string
{
    case Pending    = 'pending';
    case Accepted   = 'accepted';
    case InProgress = 'in_progress';
    case Completed  = 'completed';

    /**
     * Ruxsat etilgan status o'tishlari (oddiy state machine).
     * Bu yerda tekshirish orqali "Pending'dan to'g'ridan-to'g'ri Completed'ga"
     * kabi noto'g'ri sakrashlar bloklanadi.
     */
    private const TRANSITIONS = [
        self::Pending->value    => [self::Accepted],
        self::Accepted->value   => [self::InProgress],
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
            self::InProgress => 'Jarayonda',
            self::Completed  => 'Yakunlangan',
        };
    }
}
