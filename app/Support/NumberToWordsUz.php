<?php

namespace App\Support;

class NumberToWordsUz
{
    private const UNITS = [
        1 => 'бир', 2 => 'икки', 3 => 'уч', 4 => 'тўрт', 5 => 'беш',
        6 => 'олти', 7 => 'етти', 8 => 'саккиз', 9 => 'тўққиз',
    ];

    private const TENS = [
        1 => 'ўн', 2 => 'йигирма', 3 => 'ўттиз', 4 => 'қирқ', 5 => 'эллик',
        6 => 'олтмиш', 7 => 'етмиш', 8 => 'саксон', 9 => 'тўқсон',
    ];

    private const SCALES = ['', 'минг', 'миллион', 'миллиард', 'триллион'];

    /**
     * Pul summasini "<so'z> сўм <NN> тийин" ko'rinishida qaytaradi.
     */
    public static function money(int|float $amount): string
    {
        $soum = (int) floor($amount);
        $tiyin = (int) round(($amount - $soum) * 100);

        $words = self::words($soum);

        return trim($words).' сўм '.sprintf('%02d', $tiyin).' тийин';
    }

    /**
     * Butun sonni so'z bilan yozadi.
     */
    public static function words(int $number): string
    {
        if ($number === 0) {
            return 'ноль';
        }

        $groups = [];
        while ($number > 0) {
            $groups[] = $number % 1000;
            $number = intdiv($number, 1000);
        }

        $parts = [];
        for ($i = count($groups) - 1; $i >= 0; $i--) {
            $g = $groups[$i];
            if ($g === 0) {
                continue;
            }
            $chunk = self::threeDigits($g);
            $scale = self::SCALES[$i] ?? '';
            $parts[] = $scale !== '' ? $chunk.' '.$scale : $chunk;
        }

        return implode(' ', $parts);
    }

    private static function threeDigits(int $n): string
    {
        $h = intdiv($n, 100);
        $t = intdiv($n % 100, 10);
        $u = $n % 10;

        $words = [];
        if ($h > 0) {
            $words[] = self::UNITS[$h].' юз';
        }
        if ($t > 0) {
            $words[] = self::TENS[$t];
        }
        if ($u > 0) {
            $words[] = self::UNITS[$u];
        }

        return implode(' ', $words);
    }
}
