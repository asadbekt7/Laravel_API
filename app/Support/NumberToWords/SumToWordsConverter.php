<?php

declare(strict_types=1);

namespace App\Support\NumberToWords;

/**
 * Summani rus tilida so'z bilan ifodalaydi (shablondagi "Приёмный акт" formatiga mos):
 *
 *   4973300.00 -> "Четыре миллиона девятьсот семьдесят три тысячи триста сум 00 тийинов"
 *
 * Faqat shu blank uchun ishlatiladi, boshqa joyda qayta ishlatish kerak bo'lsa
 * interfeys chiqarib alohida joyga ko'chirish tavsiya etiladi (bo'lim 8 ga qarang).
 */
final class SumToWordsConverter
{
    private const ONES_MASCULINE = [
        '', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять',
    ];

    private const ONES_FEMININE = [
        '', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять',
    ];

    private const TEENS = [
        'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать',
        'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать',
    ];

    private const TENS = [
        '', '', 'двадцать', 'тридцать', 'сорок', 'пятьдесят',
        'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто',
    ];

    private const HUNDREDS = [
        '', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот',
        'шестьсот', 'семьсот', 'восемьсот', 'девятьсот',
    ];

    /**
     * scale => [birlik uchun forma, 2-4 uchun forma, 5-0/11-19 uchun forma, feminine?]
     */
    private const SCALES = [
        1 => ['тысяча', 'тысячи', 'тысяч', true],
        2 => ['миллион', 'миллиона', 'миллионов', false],
        3 => ['миллиард', 'миллиарда', 'миллиардов', false],
    ];

    private const TIYIN_FORMS = ['тийин', 'тийина', 'тийинов'];

    public static function convert(float|string $amount): string
    {
        $amount = round((float) $amount, 2);
        $whole  = (int) floor($amount);
        $tiyin  = (int) round(($amount - $whole) * 100);

        // 99.999 kabi holatlarda yaxlitlashdan keyin 100 tiyin chiqib qolishining oldini olamiz
        if ($tiyin === 100) {
            $whole++;
            $tiyin = 0;
        }

        $sumSentence = $whole === 0
            ? 'ноль сум'
            : trim(self::convertWhole($whole)) . ' сум';

        $tiyinWord = self::pluralForm($tiyin, self::TIYIN_FORMS);

        return sprintf('%s %02d %s', self::ucfirstMb($sumSentence), $tiyin, $tiyinWord);
    }

    private static function convertWhole(int $number): string
    {
        if ($number === 0) {
            return '';
        }

        $groups = [];
        $temp   = $number;

        while ($temp > 0) {
            $groups[] = $temp % 1000;
            $temp     = intdiv($temp, 1000);
        }

        $parts = [];

        for ($scale = count($groups) - 1; $scale >= 0; $scale--) {
            $groupNumber = $groups[$scale] ?? 0;

            if ($groupNumber === 0) {
                continue;
            }

            $feminine   = $scale === 1; // faqat "тысяча" guruhida ayollik shakli ("одна", "две")
            $groupWords = self::convertGroup($groupNumber, $feminine);

            if ($scale > 0) {
                $groupWords[] = self::pluralForm($groupNumber, self::SCALES[$scale]);
            }

            $parts[] = implode(' ', $groupWords);
        }

        return implode(' ', $parts);
    }

    /**
     * 0-999 oralig'idagi sonni so'zlarga o'giradi.
     *
     * @return list<string>
     */
    private static function convertGroup(int $number, bool $feminine): array
    {
        $words = [];

        $hundreds  = intdiv($number, 100);
        $remainder = $number % 100;

        if ($hundreds > 0) {
            $words[] = self::HUNDREDS[$hundreds];
        }

        if ($remainder >= 10 && $remainder < 20) {
            $words[] = self::TEENS[$remainder - 10];
        } else {
            $tens  = intdiv($remainder, 10);
            $units = $remainder % 10;

            if ($tens > 0) {
                $words[] = self::TENS[$tens];
            }

            if ($units > 0) {
                $words[] = $feminine ? self::ONES_FEMININE[$units] : self::ONES_MASCULINE[$units];
            }
        }

        return $words;
    }

    /**
     * Rus tilidagi standart sonlarni ko'plik qoidasi (1 / 2-4 / 5-20).
     *
     * @param array{0: string, 1: string, 2: string}|array{0: string, 1: string, 2: string, 3: bool} $forms
     */
    private static function pluralForm(int $number, array $forms): string
    {
        $mod100 = abs($number) % 100;
        $mod10  = $mod100 % 10;

        if ($mod100 > 10 && $mod100 < 20) {
            return $forms[2];
        }

        if ($mod10 > 1 && $mod10 < 5) {
            return $forms[1];
        }

        if ($mod10 === 1) {
            return $forms[0];
        }

        return $forms[2];
    }

    private static function ucfirstMb(string $string): string
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }
}
