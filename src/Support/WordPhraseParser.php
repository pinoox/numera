<?php

namespace Pino\Support;

use Pino\Numera;

class WordPhraseParser
{
    public static function weekdayKeyFromWord(Numera $numera, string $word): ?string
    {
        $word = self::normalizeToken($word);
        if ($word === '') {
            return null;
        }

        $weekdays = $numera->translateMap('weekdays', []);
        foreach ($weekdays as $key => $label) {
            if (self::normalizeToken((string)$label) === $word) {
                return $key;
            }
        }

        try {
            return WeekdayHelper::resolveKey($word);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    public static function parseNumber(Numera $numera, string $words): ParsedWordNumber
    {
        $words = trim($words);
        if ($words === '') {
            return new ParsedWordNumber(0);
        }

        $isNegative = false;
        $negativeWord = $numera->translate('negative', 'negative', camelCase: false);
        if ($negativeWord !== '' && self::startsWithPhrase($words, $negativeWord)) {
            $isNegative = true;
            $words = trim(mb_substr($words, mb_strlen($negativeWord)));
        }

        $pointWord = $numera->translate('point', 'point', camelCase: false);
        if ($pointWord !== '' && self::containsPhrase($words, $pointWord)) {
            $parts = explode($pointWord, $words, 2);
            $integerPart = (int)$numera->w2n(trim($parts[0]));
            $decimalDigits = self::parseDecimalDigitWords($numera, trim($parts[1]));

            return new ParsedWordNumber($integerPart, $decimalDigits, $isNegative);
        }

        return new ParsedWordNumber((int)$numera->w2n($words), isNegative: $isNegative);
    }

    private static function parseDecimalDigitWords(Numera $numera, string $segment): string
    {
        $segment = trim($segment);
        if ($segment === '') {
            return '';
        }

        $separator = $numera->translate('between.decimal', ' ', camelCase: false);
        $tokens = $separator !== '' && self::containsPhrase($segment, $separator)
            ? preg_split('/\s*' . preg_quote($separator, '/') . '\s*/u', $segment)
            : preg_split('/\s+/u', $segment, -1, PREG_SPLIT_NO_EMPTY);

        $numbers = $numera->dataNumber('numbers');
        $units = $numera->dataNumber('units');
        $wordToKey = self::stringTranslationFlip($numera);
        $digits = '';

        foreach ($tokens as $token) {
            $token = self::normalizeToken($token);
            if ($token === '') {
                continue;
            }
            $key = $wordToKey[$token] ?? null;
            if ($key === null) {
                continue;
            }
            foreach ($units as $index => $unitKey) {
                if ($unitKey === $key && isset($numbers[$key])) {
                    $digits .= (int)$numbers[$key];
                    break;
                }
            }
        }

        return $digits;
    }

    /** @return array<string, string> spoken word => cardinal key */
    private static function stringTranslationFlip(Numera $numera): array
    {
        $flip = [];
        foreach (array_filter($numera->getLocaleTranslates(), static fn($v) => is_string($v)) as $key => $value) {
            $normalized = self::normalizeToken($value);
            if ($normalized !== '') {
                $flip[$normalized] = $key;
            }
        }

        return $flip;
    }

    private static function normalizeToken(string $word): string
    {
        return mb_strtolower(trim($word));
    }

    private static function startsWithPhrase(string $haystack, string $needle): bool
    {
        $haystack = self::normalizeToken($haystack);
        $needle = self::normalizeToken($needle);

        return $needle !== '' && mb_strpos($haystack, $needle) === 0;
    }

    private static function containsPhrase(string $haystack, string $needle): bool
    {
        $needle = trim($needle);
        if ($needle === '') {
            return false;
        }

        return mb_strpos($haystack, $needle) !== false;
    }
}
