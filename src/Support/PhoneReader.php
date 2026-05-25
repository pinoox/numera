<?php

namespace Pino\Support;

use Pino\Numera;

final class PhoneReader
{
    public static function toPhone(Numera $numera, string $phone): string
    {
        $normalized = DigitNormalizer::toAsciiDigits(trim($phone));
        $hasPlus = str_starts_with($normalized, '+');
        $digitsOnly = $hasPlus ? substr($normalized, 1) : $normalized;

        $chunks = preg_split('/\D+/', $digitsOnly, -1, PREG_SPLIT_NO_EMPTY);
        if ($chunks === false) {
            $chunks = [];
        }

        $plus = $numera->translate('plus', 'plus', camelCase: false);
        $groupSep = ', ';
        $digitSep = $numera->translate('between', ' ', camelCase: false);

        $phrases = [];
        if ($hasPlus) {
            $phrases[] = $plus;
        }

        foreach ($chunks as $chunk) {
            $phrases[] = self::readDigits($numera, $chunk, $digitSep);
        }

        $text = implode($groupSep, array_filter($phrases));

        return $numera->applyCamelCase($text);
    }

    private static function readDigits(Numera $numera, string $digits, string $separator): string
    {
        $units = $numera->dataNumber('units');
        $words = [];

        foreach (str_split($digits) as $digit) {
            if (!ctype_digit($digit)) {
                continue;
            }
            $key = $units[(int)$digit] ?? null;
            $words[] = $numera->translate($key, $digit, camelCase: false);
        }

        return implode($separator === '' ? ' ' : $separator, $words);
    }
}
