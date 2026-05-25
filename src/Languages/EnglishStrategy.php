<?php

namespace Pino\Languages;

use Pino\Numera;

/**
 * English-specific reading rules (e.g. year pairs: 1999 → nineteen ninety-nine).
 */
final class EnglishStrategy
{
    public static function toYear(Numera $numera, int $year): string
    {
        if ($year < 0) {
            return $numera->n2w($year);
        }

        if ($year < 1000 || $year > 2099) {
            return $numera->n2w($year);
        }

        $century = intdiv($year, 100);
        $remainder = $year % 100;
        $and = $numera->translate('and', 'and', camelCase: false);

        if ($remainder === 0) {
            if ($century === 20) {
                return $numera->n2w(2000);
            }

            if ($century >= 10 && $century < 20) {
                return self::words0to99($numera, $century) . ' ' . $numera->translate('hundred', 'hundred', camelCase: false);
            }

            return $numera->n2w($year);
        }

        if ($century === 20 && $remainder < 10) {
            $parts = [$numera->n2w(2000)];
            if ($remainder > 0) {
                $parts[] = $and;
                $parts[] = self::words0to99($numera, $remainder);
            }

            return $numera->applyCamelCase(implode(' ', $parts));
        }

        $first = self::words0to99($numera, $century);
        $second = self::words0to99($numera, $remainder);

        return $numera->applyCamelCase(trim($first . ' ' . $second));
    }

    public static function words0to99(Numera $numera, int $value): string
    {
        if ($value < 0 || $value > 99) {
            return $numera->n2w($value);
        }

        return $numera->n2w($value);
    }
}
