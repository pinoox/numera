<?php

namespace Pino\Support;

final class DigitNormalizer
{
    /** @var array<string, string> */
    private const PERSIAN_ARABIC_MAP = [
        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
    ];

    public static function containsPersianOrArabicDigits(string $input): bool
    {
        return (bool)preg_match('/[\x{06F0}-\x{06F9}\x{0660}-\x{0669}]/u', $input);
    }

    public static function toAsciiDigits(string $input): string
    {
        return strtr($input, self::PERSIAN_ARABIC_MAP);
    }
}
