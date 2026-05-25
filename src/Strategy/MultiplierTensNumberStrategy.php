<?php

namespace Pino\Strategy;

use Pino\Numera;

/**
 * Locales that form tens as "{digit} {ten}" (e.g. Indonesian dua puluh = 20, lima puluh tiga = 53).
 * Default w2n adds values; this strategy treats [2–9] + ten as multiplication.
 */
class MultiplierTensNumberStrategy extends DelegatesDefaultNumberStrategy
{
    public function convertToNumber(Numera $numera, string $words, string|array|null $separators = null): int
    {
        unset($separators);
        $words = trim($words);
        if ($words === '') {
            return 0;
        }

        return $this->parsePhrase($numera, mb_strtolower($words));
    }

    private function parsePhrase(Numera $numera, string $phrase): int
    {
        $phrase = trim(preg_replace('/\s+/u', ' ', $phrase));
        if ($phrase === '') {
            return 0;
        }

        $scales = [
            ['quintillion', 1000000000000000000],
            ['quadrillion', 1000000000000000],
            ['trillion', 1000000000000000000],
            ['billion', 1000000000000],
            ['million', 1000000],
            ['thousand', 1000],
        ];

        foreach ($scales as [$scaleKey, $multiplier]) {
            $label = mb_strtolower($numera->translate($scaleKey, camelCase: false));
            $pattern = '/^(.+?)\s+' . preg_quote($label, '/') . '(?:\s+(.+))?$/u';
            if (preg_match($pattern, $phrase, $matches)) {
                $head = $this->parsePhrase($numera, $matches[1]);
                $tail = isset($matches[2]) ? $this->parsePhrase($numera, $matches[2]) : 0;

                return ($head * $multiplier) + $tail;
            }
        }

        $hundredLabel = mb_strtolower($numera->translate('hundred', camelCase: false));
        if (str_contains($phrase, $hundredLabel)) {
            $parts = explode($hundredLabel, $phrase, 2);
            $head = trim($parts[0]) === '' ? 1 : $this->parseSection($numera, trim($parts[0]));
            $tail = isset($parts[1]) ? $this->parseSection($numera, trim($parts[1])) : 0;

            return ($head * 100) + $tail;
        }

        return $this->parseSection($numera, $phrase);
    }

    private function parseSection(Numera $numera, string $phrase): int
    {
        $phrase = trim(str_replace([',', '-'], ' ', $phrase));
        if ($phrase === '') {
            return 0;
        }

        $tenWord = mb_strtolower($numera->translate('ten', camelCase: false));
        $phrase = str_replace($tenWord, ' ' . $tenWord . ' ', $phrase);
        $tokens = preg_split('/\s+/u', trim(preg_replace('/\s+/u', ' ', $phrase)), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($tokens === []) {
            return 0;
        }

        $flip = $this->wordToKeyMap($numera);
        $numbers = $numera->dataNumber('numbers');
        $sum = 0;
        $i = 0;

        while ($i < count($tokens)) {
            $matched = $this->matchLongestPhrase($tokens, $i, $flip);
            if ($matched !== null) {
                [$key, $length] = $matched;
                $value = (int)($numbers[$key] ?? 0);
                if ($value >= 20 && $value % 10 === 0) {
                    $sum += $value;
                } elseif ($value >= 10 && $value < 20) {
                    $sum += $value;
                } else {
                    $sum += $value;
                }
                $i += $length;
                continue;
            }

            $token = $tokens[$i];
            if ($token === $tenWord) {
                $i++;
                continue;
            }

            if (!isset($flip[$token])) {
                $i++;
                continue;
            }

            $digit = (int)($numbers[$flip[$token]] ?? 0);
            if ($digit >= 1 && $digit <= 9 && isset($tokens[$i + 1]) && $tokens[$i + 1] === $tenWord) {
                $base = $digit * 10;
                $i += 2;
                if (isset($tokens[$i]) && $tokens[$i] !== $tenWord && isset($flip[$tokens[$i]])) {
                    $unit = (int)($numbers[$flip[$tokens[$i]]] ?? 0);
                    if ($unit >= 1 && $unit <= 9) {
                        $base += $unit;
                        $i++;
                    }
                }
                $sum += $base;
                continue;
            }

            $sum += $digit;
            $i++;
        }

        return $sum;
    }

    /**
     * @param list<string> $tokens
     * @param array<string, string> $flip
     * @return array{0: string, 1: int}|null key and token count
     */
    private function matchLongestPhrase(array $tokens, int $start, array $flip): ?array
    {
        $maxLen = min(4, count($tokens) - $start);
        for ($len = $maxLen; $len >= 1; $len--) {
            $phrase = implode(' ', array_slice($tokens, $start, $len));
            if (isset($flip[$phrase])) {
                return [$flip[$phrase], $len];
            }
        }

        return null;
    }

    /** @return array<string, string> */
    private function wordToKeyMap(Numera $numera): array
    {
        $map = [];
        foreach (array_filter($numera->getLocaleTranslates(), static fn($v) => is_string($v)) as $key => $value) {
            $normalized = mb_strtolower(trim($value));
            if ($normalized !== '' && !isset($map[$normalized])) {
                $map[$normalized] = $key;
            }
        }

        uksort($map, static fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        return $map;
    }
}
