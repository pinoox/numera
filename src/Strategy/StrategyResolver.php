<?php

namespace Pino\Strategy;

use Pino\LocaleRegistry;

class StrategyResolver
{
    public const DEFAULT = 'default';

    public const GERMAN = 'german';

    public const FRENCH = 'french';

    public const MULTIPLIER_TENS = 'multiplier_tens';

    private const MAP = [
        self::DEFAULT => DefaultNumberStrategy::class,
        self::GERMAN => GermanNumberStrategy::class,
        self::FRENCH => FrenchNumberStrategy::class,
        self::MULTIPLIER_TENS => MultiplierTensNumberStrategy::class,
    ];

    public static function resolve(string $locale, ?array $meta = null): NumberStrategyInterface
    {
        $strategy = self::strategyName($locale, $meta);
        $class = self::MAP[$strategy] ?? self::MAP[self::DEFAULT];

        return new $class();
    }

    public static function strategyName(string $locale, ?array $meta = null): string
    {
        $fromMeta = is_array($meta) ? ($meta['strategy'] ?? null) : null;
        if (is_string($fromMeta) && $fromMeta !== '') {
            $name = strtolower($fromMeta);

            return array_key_exists($name, self::MAP) ? $name : self::DEFAULT;
        }

        $parent = LocaleRegistry::getParentLocale($locale);
        if ($parent !== null) {
            return self::strategyName($parent, LocaleRegistry::getLocaleMeta($parent));
        }

        return self::DEFAULT;
    }

    public static function isGerman(string $locale, ?array $meta = null): bool
    {
        return self::strategyName($locale, $meta) === self::GERMAN;
    }

    public static function isFrench(string $locale, ?array $meta = null): bool
    {
        return self::strategyName($locale, $meta) === self::FRENCH;
    }

    public static function isMultiplierTens(string $locale, ?array $meta = null): bool
    {
        return self::strategyName($locale, $meta) === self::MULTIPLIER_TENS;
    }

    public static function usesCustomNumberStrategy(string $locale, ?array $meta = null): bool
    {
        return self::strategyName($locale, $meta) !== self::DEFAULT;
    }
}
