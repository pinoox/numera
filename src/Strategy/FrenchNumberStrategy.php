<?php

namespace Pino\Strategy;

use Pino\Numera;

/**
 * French w2n: vigesimal 80–99 (quatre-vingt-*), soixante-dix 70–79, hyphen compounds.
 * n2w uses the default strategy (lang file maps seventy → soixante-dix, etc.).
 */
class FrenchNumberStrategy extends DelegatesDefaultNumberStrategy
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
            ['billion', 1000000000],
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
        $pattern = '/^(.+?)\s+' . preg_quote($hundredLabel, '/') . '(?:\s+(.+))?$/u';
        if (preg_match($pattern, $phrase, $matches)) {
            $head = $matches[1] === '' ? 1 : $this->parseUnder100($numera, $matches[1]);
            $tail = isset($matches[2]) ? $this->parseUnder100($numera, $matches[2]) : 0;

            return ($head * 100) + $tail;
        }

        return $this->parseUnder100($numera, $phrase);
    }

    private function parseUnder100(Numera $numera, string $phrase): int
    {
        $phrase = trim(preg_replace('/\s+/u', ' ', $phrase));
        if ($phrase === '') {
            return 0;
        }

        $phrase = preg_replace('/\s+et\s+/u', '-', $phrase);

        $ninety = preg_quote(mb_strtolower($numera->translate('ninety', camelCase: false)), '/');
        $eighty = preg_quote(mb_strtolower($numera->translate('eighty', camelCase: false)), '/');
        $seventy = preg_quote(mb_strtolower($numera->translate('seventy', camelCase: false)), '/');
        $sixty = preg_quote(mb_strtolower($numera->translate('sixty', camelCase: false)), '/');

        if (preg_match('/^' . $ninety . '[- ](.+)$/u', $phrase, $m)) {
            return 90 + $this->parseUnder100($numera, $m[1]);
        }

        if (preg_match('/^' . $ninety . '$/u', $phrase)) {
            return 90;
        }

        if (preg_match('/^' . $eighty . '[- ](.+)$/u', $phrase, $m)) {
            return 80 + $this->parseUnder100($numera, $m[1]);
        }

        if (preg_match('/^' . $eighty . '$/u', $phrase)) {
            return 80;
        }

        if (preg_match('/^' . $seventy . '[- ](.+)$/u', $phrase, $m)) {
            return 70 + $this->parseUnder100($numera, $m[1]);
        }

        if (preg_match('/^' . $seventy . '$/u', $phrase)) {
            return 70;
        }

        if (preg_match('/^' . $sixty . '[- ](.+)$/u', $phrase, $m)) {
            return 60 + $this->parseUnder100($numera, $m[1]);
        }

        if (preg_match('/^' . $sixty . '$/u', $phrase)) {
            return 60;
        }

        return $this->parseCompoundTokens($numera, $phrase);
    }

    private function parseCompoundTokens(Numera $numera, string $phrase): int
    {
        $phrase = str_replace(['-', ','], ' ', $phrase);
        $tokens = preg_split('/\s+/u', $phrase, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if ($tokens === []) {
            return 0;
        }

        $flip = $this->wordToKeyMap($numera);
        $numbers = $numera->dataNumber('numbers');
        $sum = 0;
        $current = 0;

        foreach ($tokens as $token) {
            if (!isset($flip[$token])) {
                continue;
            }
            $key = $flip[$token];
            $value = (int)($numbers[$key] ?? 0);

            if ($value >= 20 && $value < 100 && $value % 10 === 0) {
                $sum += $current;
                $current = $value;
                continue;
            }

            if ($value >= 100) {
                $sum += $current;
                $current = $value;
                continue;
            }

            $current += $value;
        }

        return $sum + $current;
    }

    /** @return array<string, string> */
    private function wordToKeyMap(Numera $numera): array
    {
        $map = [];
        foreach (array_filter($numera->getLocaleTranslates(), static fn($v) => is_string($v)) as $key => $value) {
            $normalized = mb_strtolower(trim($value));
            if ($normalized !== '') {
                $map[$normalized] = $key;
            }
        }

        return $map;
    }
}
