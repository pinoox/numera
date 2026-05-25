<?php

namespace Pino\Support;

final class InputFormatDetector
{
    public const FORMAT_DEFAULT = 'default';
    public const FORMAT_EUROPEAN = 'european';
    public const FORMAT_SWISS = 'swiss';
    public const FORMAT_PERSIAN = 'persian';
    public const FORMAT_UNDERSCORE = 'underscore';

    public static function detect(string $input): string
    {
        $trimmed = trim($input);

        if ($trimmed === '') {
            return self::FORMAT_DEFAULT;
        }

        if (DigitNormalizer::containsPersianOrArabicDigits($trimmed)) {
            return self::FORMAT_PERSIAN;
        }

        if (str_contains($trimmed, '_') && preg_match('/\d_\d/', $trimmed)) {
            return self::FORMAT_UNDERSCORE;
        }

        if (preg_match("/\d{1,3}('\d{3})+([.,]\d+)?/", $trimmed)) {
            return self::FORMAT_SWISS;
        }

        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $trimmed)
            || (substr_count($trimmed, '.') >= 2 && str_contains($trimmed, ','))) {
            return self::FORMAT_EUROPEAN;
        }

        return self::FORMAT_DEFAULT;
    }

    public static function normalize(string $input, ?string $format = null): string
    {
        $format ??= self::detect($input);
        $normalized = DigitNormalizer::toAsciiDigits(trim($input));

        return match ($format) {
            self::FORMAT_UNDERSCORE => str_replace('_', '', $normalized),
            self::FORMAT_SWISS => self::normalizeSwiss($normalized),
            self::FORMAT_EUROPEAN => self::normalizeEuropean($normalized),
            self::FORMAT_PERSIAN => $normalized,
            default => $normalized,
        };
    }

    private static function normalizeSwiss(string $input): string
    {
        $input = str_replace(["\u{00A0}", ' '], '', $input);
        $input = str_replace("'", '', $input);

        return $input;
    }

    private static function normalizeEuropean(string $input): string
    {
        $input = str_replace(["\u{00A0}", ' '], '', $input);

        if (!str_contains($input, ',')) {
            return str_replace('.', '', $input);
        }

        $lastComma = strrpos($input, ',');
        $integerPart = substr($input, 0, $lastComma);
        $decimalPart = substr($input, $lastComma + 1);
        $integerPart = str_replace('.', '', $integerPart);

        return $integerPart . '.' . $decimalPart;
    }
}
