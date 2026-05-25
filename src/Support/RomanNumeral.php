<?php

namespace Pino\Support;

final class RomanNumeral
{
    private const MIN = 1;
    private const MAX = 3999;

    /** @var list<array{0: int, 1: string}> */
    private const MAP = [
        [1000, 'M'],
        [900, 'CM'],
        [500, 'D'],
        [400, 'CD'],
        [100, 'C'],
        [90, 'XC'],
        [50, 'L'],
        [40, 'XL'],
        [10, 'X'],
        [9, 'IX'],
        [5, 'V'],
        [4, 'IV'],
        [1, 'I'],
    ];

    public static function toRoman(int $number): string
    {
        if ($number < self::MIN || $number > self::MAX) {
            throw new \InvalidArgumentException(
                'Roman numerals support integers from ' . self::MIN . ' to ' . self::MAX . ' only.'
            );
        }

        $result = '';
        $remaining = $number;

        foreach (self::MAP as [$value, $symbol]) {
            while ($remaining >= $value) {
                $result .= $symbol;
                $remaining -= $value;
            }
        }

        return $result;
    }

    public static function fromRoman(string $roman): int
    {
        $roman = strtoupper(trim($roman));
        if ($roman === '' || !preg_match('/^[IVXLCDM]+$/', $roman)) {
            throw new \InvalidArgumentException('Invalid Roman numeral: ' . $roman);
        }

        $values = ['I' => 1, 'V' => 5, 'X' => 10, 'L' => 50, 'C' => 100, 'D' => 500, 'M' => 1000];
        $total = 0;
        $length = strlen($roman);

        for ($i = 0; $i < $length; $i++) {
            $current = $values[$roman[$i]];
            $next = $i + 1 < $length ? $values[$roman[$i + 1]] : 0;
            if ($next > $current) {
                $total -= $current;
            } else {
                $total += $current;
            }
        }

        if ($total < self::MIN || $total > self::MAX) {
            throw new \InvalidArgumentException('Roman numeral out of supported range.');
        }

        if (self::toRoman($total) !== $roman) {
            throw new \InvalidArgumentException('Invalid Roman numeral: ' . $roman);
        }

        return $total;
    }
}
