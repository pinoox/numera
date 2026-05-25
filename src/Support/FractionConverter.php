<?php

namespace Pino\Support;

use Pino\Numera;

final class FractionConverter
{
    private const DENOMINATORS = [2, 3, 4, 5, 8];

    private const TOLERANCE = 0.001;

    public static function toFraction(Numera $numera, float $number): string
    {
        $negative = $number < 0;
        $number = abs($number);

        $integer = (int)floor($number);
        $fractional = $number - $integer;

        if ($fractional < self::TOLERANCE) {
            $words = $numera->n2w($negative ? -$integer : $integer);

            return $numera->applyCamelCase($words);
        }

        $match = self::matchFraction($fractional);
        if ($match === null) {
            return $numera->n2w($negative ? -$number : $number);
        }

        [$numerator, $denominator] = $match;
        $phrase = self::buildFractionPhrase($numera, $numerator, $denominator);

        if ($integer === 0) {
            $result = $phrase;
        } else {
            $and = self::fractionWord($numera, 'and', 'and');
            $whole = self::fractionWord($numera, 'whole_' . self::wholeKey($integer), $numera->n2w($integer));
            if ($whole === '') {
                $whole = $numera->n2w($integer);
            }
            $isFa = $numera->getLocale() === 'fa' || str_starts_with($numera->getLocale(), 'fa-');
            if ($isFa) {
                $result = trim($whole . ' ' . $and . ' ' . $phrase);
            } else {
                $article = self::fractionWord($numera, 'article', 'a');
                $result = trim($whole . ' ' . $and . ' ' . $article . ' ' . $phrase);
            }
        }

        if ($negative) {
            $result = $numera->translate('negative', 'negative', camelCase: false) . ' ' . $result;
        }

        return $numera->applyCamelCase($result);
    }

    private static function wholeKey(int $integer): string
    {
        $keys = [
            1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five',
            6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten',
        ];

        return $keys[$integer] ?? 'other';
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private static function matchFraction(float $fractional): ?array
    {
        foreach (self::DENOMINATORS as $denominator) {
            $scaled = $fractional * $denominator;
            $numerator = (int)round($scaled);
            if ($numerator < 1 || $numerator >= $denominator) {
                continue;
            }
            if (abs($scaled - $numerator) <= self::TOLERANCE) {
                return [$numerator, $denominator];
            }
        }

        return null;
    }

    private static function buildFractionPhrase(Numera $numera, int $numerator, int $denominator): string
    {
        $isFa = $numera->getLocale() === 'fa' || str_starts_with($numera->getLocale(), 'fa-');

        if ($isFa && $denominator === 2 && $numerator === 1) {
            return self::fractionWord($numera, 'half', 'نیم');
        }

        $denomKeys = [
            2 => ['half', 'halves'],
            3 => ['third', 'thirds'],
            4 => ['quarter', 'quarters'],
            5 => ['fifth', 'fifths'],
            8 => ['eighth', 'eighths'],
        ];

        [$singular, $plural] = $denomKeys[$denominator];
        $denomWord = $numerator === 1
            ? self::fractionWord($numera, $singular, $singular)
            : self::fractionWord($numera, $plural, $plural);

        if ($numerator === 1) {
            $one = self::fractionWord($numera, 'one', 'one');

            return trim($one . ' ' . $denomWord);
        }

        $numWord = $numera->n2w($numerator);

        return trim($numWord . ' ' . $denomWord);
    }

    private static function fractionWord(Numera $numera, string $key, string $default): string
    {
        $map = $numera->translateMap('fractions', []);

        return (string)($map[$key] ?? $default);
    }
}
