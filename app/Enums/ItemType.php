<?php


namespace App\Enums;

enum ItemType: string
{
    case ASOSIY_VOSITA = 'asosiy_vosita';
    case RASXOD = 'rasxod';

    public function label(): string
    {
        return match($this) {
            self::ASOSIY_VOSITA => 'Asosiy vosita',
            self::RASXOD => 'Rasxod mahsulot',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
