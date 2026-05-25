<?php

namespace Pino\Support;

class WeekdayHelper
{
    public const KEYS = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    ];

    /** ISO-8601: 1 = Monday … 7 = Sunday */
    private const ISO_NUMERIC = [
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        7 => 'sunday',
    ];

    /** PHP date('w'): 0 = Sunday … 6 = Saturday */
    private const PHP_W = [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
    ];

    public static function resolveKey(int|string $day): string
    {
        if (is_int($day) || (is_string($day) && ctype_digit(trim($day)))) {
            $n = (int)$day;
            if (array_key_exists($n, self::ISO_NUMERIC)) {
                return self::ISO_NUMERIC[$n];
            }
            if (array_key_exists($n, self::PHP_W)) {
                return self::PHP_W[$n];
            }
        }

        $key = strtolower(trim((string)$day));
        if (in_array($key, self::KEYS, true)) {
            return $key;
        }

        throw new \InvalidArgumentException(
            'Invalid weekday. Use monday–sunday, ISO 1–7 (Monday=1), or PHP date("w") 0–6 (Sunday=0).'
        );
    }

    /** @return list<string> */
    public static function orderedKeys(array $localeMeta = []): array
    {
        $startsOn = strtolower((string)($localeMeta['week_starts_on'] ?? 'monday'));
        if (!in_array($startsOn, self::KEYS, true)) {
            $startsOn = 'monday';
        }

        $keys = self::KEYS;
        $index = array_search($startsOn, $keys, true);
        if ($index === false || $index === 0) {
            return $keys;
        }

        return array_merge(array_slice($keys, $index), array_slice($keys, 0, $index));
    }

    /** @return list<string> */
    public static function missingKeys(array $weekdays): array
    {
        $missing = [];
        foreach (self::KEYS as $key) {
            if (empty($weekdays[$key]) || !is_string($weekdays[$key])) {
                $missing[] = 'weekdays.' . $key;
            }
        }

        return $missing;
    }
}
