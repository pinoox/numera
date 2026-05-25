<?php

namespace Pino;

final class UnitConfig
{
    private const DEFINITIONS = [
        'kg' => ['singular' => 'kilogram', 'plural' => 'kilograms'],
        'g' => ['singular' => 'gram', 'plural' => 'grams'],
        'km' => ['singular' => 'kilometer', 'plural' => 'kilometers'],
        'm' => ['singular' => 'meter', 'plural' => 'meters'],
        'cm' => ['singular' => 'centimeter', 'plural' => 'centimeters'],
        'hour' => ['singular' => 'hour', 'plural' => 'hours'],
        'minute' => ['singular' => 'minute', 'plural' => 'minutes'],
        'second' => ['singular' => 'second', 'plural' => 'seconds'],
        'day' => ['singular' => 'day', 'plural' => 'days'],
        'week' => ['singular' => 'week', 'plural' => 'weeks'],
        'month' => ['singular' => 'month', 'plural' => 'months'],
        'year' => ['singular' => 'year', 'plural' => 'years'],
    ];

    public static function exists(string $unit): bool
    {
        return isset(self::DEFINITIONS[strtolower($unit)]);
    }

    public static function get(string $unit): array
    {
        $unit = strtolower($unit);
        if (!isset(self::DEFINITIONS[$unit])) {
            throw new \InvalidArgumentException("Unsupported unit: {$unit}");
        }

        return self::DEFINITIONS[$unit];
    }

    public static function supportedUnits(): array
    {
        return array_keys(self::DEFINITIONS);
    }
}
