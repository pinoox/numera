<?php

namespace Pino;

final class CurrencyConfig
{
    private const DEFINITIONS = [
        'USD' => [
            'decimals' => 2,
            'has_sub_unit' => true,
            'main_singular' => 'dollar',
            'main_plural' => 'dollars',
            'sub_singular' => 'cent',
            'sub_plural' => 'cents',
        ],
        'EUR' => [
            'decimals' => 2,
            'has_sub_unit' => true,
            'main_singular' => 'euro',
            'main_plural' => 'euros',
            'sub_singular' => 'cent',
            'sub_plural' => 'cents',
        ],
        'GBP' => [
            'decimals' => 2,
            'has_sub_unit' => true,
            'main_singular' => 'pound',
            'main_plural' => 'pounds',
            'sub_singular' => 'penny',
            'sub_plural' => 'pence',
        ],
        'IRR' => [
            'decimals' => 0,
            'has_sub_unit' => false,
            'main_singular' => 'rial',
            'main_plural' => 'rials',
            'sub_singular' => '',
            'sub_plural' => '',
        ],
        'IRT' => [
            'decimals' => 0,
            'has_sub_unit' => false,
            'main_singular' => 'toman',
            'main_plural' => 'tomans',
            'sub_singular' => '',
            'sub_plural' => '',
        ],
    ];

    public static function exists(string $code): bool
    {
        return isset(self::DEFINITIONS[strtoupper($code)]);
    }

    public static function get(string $code): array
    {
        $code = strtoupper($code);
        if (!isset(self::DEFINITIONS[$code])) {
            throw new \InvalidArgumentException("Unsupported currency code: {$code}");
        }

        return self::DEFINITIONS[$code];
    }

    public static function supportedCodes(): array
    {
        return array_keys(self::DEFINITIONS);
    }
}
