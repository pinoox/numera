<?php

namespace Pino\Support;

use Pino\Languages\EnglishStrategy;
use Pino\Numera;

final class YearReader
{
    public static function toYear(Numera $numera, int $year): string
    {
        $locale = $numera->getLocale();
        if ($locale === 'en' || str_starts_with($locale, 'en-')) {
            return EnglishStrategy::toYear($numera, $year);
        }

        return $numera->n2w($year);
    }
}
