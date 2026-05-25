<?php

namespace Pino\Support;

final class NumberInputParser
{
    public static function detectFormat(string $input): string
    {
        return InputFormatDetector::detect($input);
    }

    public static function parse(int|float|string $input): ParsedNumber
    {
        $negative = false;
        $str = self::toNormalizedString($input);

        if (is_string($input) && !is_numeric($input)) {
            $str = InputFormatDetector::normalize($str);
        }

        if ($str === '' || $str === '-') {
            return new ParsedNumber(false, 0, '');
        }

        if ($str[0] === '-') {
            $negative = true;
            $str = substr($str, 1);
        }

        $str = str_replace([' ', "\u{00A0}"], '', $str);

        [$integerStr, $decimalDigits] = self::splitIntegerAndDecimal($str);

        $integerStr = str_replace(',', '', $integerStr);
        $integerPart = $integerStr === '' ? 0 : (int)$integerStr;

        return new ParsedNumber($negative, $integerPart, $decimalDigits);
    }

    private static function toNormalizedString(int|float|string $input): string
    {
        if (is_int($input)) {
            return (string)$input;
        }

        if (is_float($input)) {
            if (is_nan($input) || is_infinite($input)) {
                return '0';
            }

            $str = rtrim(rtrim(sprintf('%.10F', $input), '0'), '.');

            return $str === '' || $str === '-0' ? '0' : $str;
        }

        $trimmed = trim((string)$input);

        if ($trimmed !== '' && !is_numeric($trimmed)) {
            return InputFormatDetector::normalize($trimmed);
        }

        return $trimmed;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function splitIntegerAndDecimal(string $str): array
    {
        $dotCount = substr_count($str, '.');
        $commaCount = substr_count($str, ',');

        if ($dotCount > 0 && $commaCount > 0) {
            return self::splitMixedSeparators($str);
        }

        if ($dotCount > 1) {
            return [str_replace('.', '', $str), ''];
        }

        if ($dotCount === 1) {
            return self::splitSingleDot($str);
        }

        if ($commaCount > 1 || ($commaCount === 1 && preg_match('/,\d{3}(?:,\d{3})*$/', $str))) {
            return [str_replace(',', '', $str), ''];
        }

        if ($commaCount === 1) {
            [$intPart, $frac] = explode(',', $str, 2);

            if (strlen($frac) <= 2) {
                return [$intPart, $frac];
            }

            return [str_replace(',', '', $str), ''];
        }

        return [$str, ''];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function splitMixedSeparators(string $str): array
    {
        $lastDot = strrpos($str, '.');
        $lastComma = strrpos($str, ',');

        if ($lastDot > $lastComma) {
            $parts = explode('.', $str);
            $decimal = (string)array_pop($parts);
            $integer = str_replace(',', '', implode('', $parts));

            return [$integer, $decimal];
        }

        $parts = explode(',', $str);
        $decimal = (string)array_pop($parts);
        $integer = str_replace('.', '', implode('', $parts));

        return [$integer, $decimal];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function splitSingleDot(string $str): array
    {
        [$intPart, $frac] = explode('.', $str, 2);

        if (strlen($frac) <= 2) {
            return [$intPart, $frac];
        }

        if (strlen($frac) === 3 && strlen($intPart) <= 3) {
            return [str_replace('.', '', $str), ''];
        }

        return [str_replace('.', '', $str), ''];
    }
}
