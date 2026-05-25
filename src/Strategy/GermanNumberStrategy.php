<?php

namespace Pino\Strategy;

use Pino\Numera;

class GermanNumberStrategy implements NumberStrategyInterface
{
    public function convertToWords(Numera $numera, int $num): string
    {
        if ($num === 0) {
            return $numera->translate('zero');
        }

        $thousands = $numera->dataNumber('thousands');
        $result = '';
        $i = 0;

        while ($num > 0) {
            $part = $num % 1000;
            if ($part !== 0) {
                $words = $this->convertUnder1000($numera, $part);
                if ($i === 0) {
                    $result = $words . $result;
                } elseif ($i === 1) {
                    $result = ($part === 1 ? 'ein' : $words) . 'tausend' . $result;
                } else {
                    $scale = $numera->translate($thousands[$i]);
                    $result = $words . ' ' . $scale . ($result !== '' ? ' ' . $result : '');
                }
            }
            $num = (int)($num / 1000);
            $i++;
        }

        return $numera->applyCamelCase($result);
    }

    public function convertToSummary(Numera $numera, int|string $num): string
    {
        $num = $this->normalizeNumericInput($num);
        $thousands = $numera->dataNumber('thousands');

        if ($num === 0) {
            return '0';
        }

        $parts = explode(',', number_format($num));
        $groups = array_map('intval', $parts);

        $scaleCount = count($groups) - 1;
        $parts = [];
        foreach ($groups as $i => $group) {
            $scaleKey = $thousands[$scaleCount - $i];
            if ($scaleKey === '') {
                $parts[] = (string)$group;
            } else {
                $scaleLabel = $numera->translate($scaleKey, camelCase: false);
                $parts[] = $group . ' ' . $scaleLabel;
            }
        }

        return implode(', ', $parts);
    }

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
        $phrase = trim($phrase);
        if ($phrase === '') {
            return 0;
        }

        $scales = [
            ['quintillion', 1000000000000000000],
            ['quadrillion', 1000000000000000],
            ['trillion', 1000000000000000000],
            ['billion', 1000000000],
            ['million', 1000000],
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

        return $this->parseCompound($numera, $phrase);
    }

    private function convertUnder1000(Numera $numera, int $num): string
    {
        $units = $numera->dataNumber('units');
        $teens = $numera->dataNumber('teens');
        $tens = $numera->dataNumber('tens');
        $hundreds = $numera->dataNumber('hundreds');

        if ($num < 10) {
            return $this->unitWord($numera, $num, standalone: true);
        }
        if ($num < 20) {
            return $numera->translate($teens[$num - 10], camelCase: false);
        }
        if ($num < 100) {
            $unit = $num % 10;
            $ten = (int)($num / 10);
            if ($unit === 0) {
                return $numera->translate($tens[$ten], camelCase: false);
            }

            return $this->unitWord($numera, $unit)
                . 'und'
                . $numera->translate($tens[$ten], camelCase: false);
        }

        $hundred = (int)($num / 100);
        $remainder = $num % 100;
        $hundredWord = $numera->translate($hundreds[$hundred], camelCase: false);

        if ($remainder === 0) {
            return $hundredWord;
        }

        return $hundredWord . $this->convertUnder1000($numera, $remainder);
    }

    private function unitWord(Numera $numera, int $digit, bool $standalone = false): string
    {
        $units = $numera->dataNumber('units');
        if ($digit === 1 && !$standalone) {
            return 'ein';
        }

        return $numera->translate($units[$digit], camelCase: false);
    }

    private function normalizeNumericInput(int|string $num): int
    {
        $num = str_replace(['.', ',', ' '], '', (string)$num);

        return (int)trim($num);
    }

    private function parseCompound(Numera $numera, string $word): int
    {
        $word = mb_strtolower(trim($word));
        if ($word === '') {
            return 0;
        }

        if (preg_match('/^(.*?)tausend(.*)$/u', $word, $matches)) {
            $prefix = $matches[1];
            $suffix = $matches[2];
            $value = ($prefix === '' ? 1 : $this->parseUnder1000($numera, $prefix)) * 1000;

            return $value + ($suffix === '' ? 0 : $this->parseUnder1000($numera, $suffix));
        }

        return $this->parseUnder1000($numera, $word);
    }

    private function parseUnder1000(Numera $numera, string $word): int
    {
        $word = mb_strtolower(trim($word));
        if ($word === '') {
            return 0;
        }

        if (preg_match('/^(.*?)hundert(.*)$/u', $word, $matches)) {
            $prefix = $matches[1] === '' ? 1 : $this->parseUnder1000($numera, $matches[1]);
            $suffix = $matches[2] === '' ? 0 : $this->parseUnder1000($numera, $matches[2]);

            return ($prefix * 100) + $suffix;
        }

        $tens = $numera->dataNumber('tens');
        foreach (array_slice($tens, 2) as $tenKey) {
            $tenWord = $numera->translate($tenKey, camelCase: false);
            if (str_ends_with($word, 'und' . $tenWord)) {
                $unitPart = mb_substr($word, 0, mb_strlen($word) - mb_strlen('und' . $tenWord));

                return $this->parseUnit($numera, $unitPart) + (int)$numera->dataNumber('numbers')[$tenKey];
            }
            if ($word === $tenWord) {
                return (int)$numera->dataNumber('numbers')[$tenKey];
            }
        }

        return $this->parseUnit($numera, $word);
    }

    private function parseUnit(Numera $numera, string $word): int
    {
        $word = mb_strtolower(trim($word));
        if ($word === '' || $word === 'ein') {
            return $word === 'ein' ? 1 : 0;
        }

        $translations = array_flip(array_map(
            'mb_strtolower',
            array_filter($numera->getLocaleTranslates(), static fn($value) => is_string($value))
        ));
        $numbers = $numera->dataNumber('numbers');

        if (!isset($translations[$word])) {
            return 0;
        }

        $key = $translations[$word];

        return (int)($numbers[$key] ?? 0);
    }
}
