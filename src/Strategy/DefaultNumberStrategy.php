<?php

namespace Pino\Strategy;

use Pino\Numera;

class DefaultNumberStrategy implements NumberStrategyInterface
{
    public function convertToWords(Numera $numera, int $num): string
    {
        $units = $numera->dataNumber('units');
        $teens = $numera->dataNumber('teens');
        $tens = $numera->dataNumber('tens');
        $hundreds = $numera->dataNumber('hundreds');
        $thousands = $numera->dataNumber('thousands');

        if ($num < 10) {
            return $this->finalize($numera, $numera->translate($units[$num]));
        }
        if ($num < 20) {
            return $this->finalize($numera, $numera->translate($teens[$num - 10]));
        }
        if ($num < 100) {
            return $this->finalize($numera, $numera->translate($tens[(int)($num / 10)])
                . ($num % 10 !== 0 ? '{ten}' . $numera->translate($units[$num % 10]) : ''));
        }
        if ($num < 1000) {
            return $this->finalize($numera, $numera->translate($hundreds[(int)($num / 100)])
                . ($num % 100 !== 0 ? '{hundred}' . $this->convertToWords($numera, $num % 100) : ''));
        }

        $result = '';
        for ($i = 0; $num > 0; $i++) {
            $part = $num % 1000;
            if ($part !== 0) {
                $result = $this->convertToWords($numera, $part)
                    . '{thousand}'
                    . $numera->translate($thousands[$i])
                    . ($result ? '{part}' : '')
                    . $result;
            }
            $num = (int)($num / 1000);
        }

        return $this->finalize($numera, $result);
    }

    private function finalize(Numera $numera, string $result): string
    {
        return $numera->replaceSeparator($result);
    }

    public function convertToSummary(Numera $numera, int|string $num): string
    {
        $thousands = $numera->dataNumber('thousands');
        $num = str_replace(',', '', (string)$num);
        $num = number_format((int)$num);
        $parts = explode(',', $num);
        $count = count($parts) - 1;
        $result = '';
        foreach ($parts as $i => $part) {
            $result .= $i === 0
                ? $part . '{thousand}' . $numera->translate($thousands[$count - $i])
                : '{part}' . $part . '{thousand}' . $numera->translate($thousands[$count - $i]);
        }

        return $numera->replaceSeparator($result);
    }

    public function convertToNumber(Numera $numera, string $words, string|array|null $separators = null): int
    {
        $translations = array_flip(array_filter($numera->getLocaleTranslates(), static fn($value) => is_string($value)));
        $wordsArray = $numera->getArrayBySeparator($words, $separators);
        $number = 0;
        $numbers = $numera->dataNumber('numbers');
        $tempNumber = 0;

        foreach ($wordsArray as $word) {
            $word = mb_strtolower($word);

            if (is_numeric($word)) {
                $num = (int)$word;
            } else {
                if (!isset($translations[$word])) {
                    continue;
                }

                $word = $translations[$word];

                if (!isset($numbers[$word])) {
                    continue;
                }

                $num = (int)$numbers[$word];
            }

            if ($word === 'hundred') {
                $tempNumber = $tempNumber > 0 ? $tempNumber : 1;
                $tempNumber *= $num;
            } elseif (in_array($word, ['thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion'], true)) {
                $tempNumber = $tempNumber > 0 ? $tempNumber : 1;
                $tempNumber *= $num;
                $number += $tempNumber;
                $tempNumber = 0;
            } else {
                $tempNumber += $num;
            }
        }

        return $number + $tempNumber;
    }
}
