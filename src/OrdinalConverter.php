<?php

namespace Pino;

final class OrdinalConverter
{
    public function __construct(private Numera $numera)
    {
    }

    public function toOrdinal(int $number): string
    {
        if ($number === 0) {
            return $this->numera->translate('ordinals.zero', $this->numera->translate('zero'), camelCase: false);
        }

        $locale = $this->numera->getLocale();

        if ($locale === 'fa' || str_starts_with($locale, 'fa-')) {
            return $this->toOrdinalPersian($number);
        }

        return $this->toOrdinalEnglish($number);
    }

    private function toOrdinalEnglish(int $number): string
    {
        $exceptions = $this->numera->translateMap('ordinal_exceptions', []);
        if (isset($exceptions[$number])) {
            return $this->numera->applyCamelCase((string)$exceptions[$number]);
        }

        $cardinal = $this->numera->convertToWords(abs($number));
        if ($number < 0) {
            $cardinal = $this->numera->convertToWords($number);
        }

        $cardinalLower = strtolower($cardinal);
        $ordinalWords = $this->numera->translateMap('ordinals', []);

        if ($key = $this->cardinalKeyFromWord($cardinalLower)) {
            if (isset($ordinalWords[$key])) {
                return $this->numera->applyCamelCase((string)$ordinalWords[$key]);
            }
        }

        if (preg_match('/^(.+?)([\s-])([^-\s]+)$/u', $cardinalLower, $matches)) {
            $prefix = $matches[1];
            $separator = $matches[2];
            $lastWord = $matches[3];
            $lastKey = $this->cardinalKeyFromWord($lastWord);

            if ($lastKey !== null && isset($ordinalWords[$lastKey])) {
                $result = $prefix . $separator . $ordinalWords[$lastKey];

                return $this->numera->applyCamelCase($result);
            }
        }

        if (isset($ordinalWords[$cardinalLower])) {
            return $this->numera->applyCamelCase((string)$ordinalWords[$cardinalLower]);
        }

        $suffix = $this->englishSuffixFor(abs($number));
        $result = $cardinalLower . $suffix;

        return $this->numera->applyCamelCase($result);
    }

    private function englishSuffixFor(int $number): string
    {
        $suffixes = $this->numera->translateMap('ordinal_suffixes', ['th', 'st', 'nd', 'rd', 'th']);
        $mod100 = $number % 100;
        if ($mod100 >= 11 && $mod100 <= 13) {
            return (string)($suffixes[0] ?? 'th');
        }

        return match ($number % 10) {
            1 => (string)($suffixes[1] ?? 'st'),
            2 => (string)($suffixes[2] ?? 'nd'),
            3 => (string)($suffixes[3] ?? 'rd'),
            default => (string)($suffixes[0] ?? 'th'),
        };
    }

    private function toOrdinalPersian(int $number): string
    {
        $exceptions = $this->numera->translateMap('ordinal_exceptions', []);
        if (isset($exceptions[$number])) {
            return (string)$exceptions[$number];
        }

        $cardinal = $this->numera->convertToWords($number);
        $suffix = (string)$this->numera->translate('ordinal_suffix', 'م', camelCase: false);

        if ($number < 0) {
            return $cardinal;
        }

        return $cardinal . $suffix;
    }

    private function cardinalKeyFromWord(string $word): ?string
    {
        $word = mb_strtolower(trim($word));
        if ($word === '') {
            return null;
        }

        $translations = $this->numera->getLocaleTranslates();
        foreach ($translations as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                continue;
            }
            if (mb_strtolower($value) === $word) {
                return $key;
            }
        }

        return null;
    }
}
