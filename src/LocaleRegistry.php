<?php

namespace Pino;

use Pino\Support\WeekdayHelper;
use Pino\Strategy\StrategyResolver;

class LocaleRegistry
{
    private const REQUIRED_KEYS = [
        'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
        'seventeen', 'eighteen', 'nineteen', 'twenty', 'thirty', 'forty', 'fifty',
        'sixty', 'seventy', 'eighty', 'ninety', 'hundred', 'thousand', 'million',
        'billion', 'trillion', 'quadrillion', 'quintillion',
    ];

    /** v1.2+ keys — each locale file under src/lang/ must define these. */
    private const EXTENSION_KEYS = [
        'negative', 'point', 'and', 'ordinals', 'currencies', 'units', 'weekdays',
    ];

    public static function getIso639_1Codes(): array
    {
        return include __DIR__ . '/data/iso639-1.php';
    }

    public static function getLangDirectory(): string
    {
        return __DIR__ . '/lang';
    }

    public static function getAvailableLocales(): array
    {
        $locales = [];
        foreach (glob(self::getLangDirectory() . '/*.php') ?: [] as $file) {
            $code = basename($file, '.php');
            if (!str_starts_with($code, '_')) {
                $locales[] = $code;
            }
        }

        sort($locales);

        return array_values($locales);
    }

    public static function hasLocale(string $locale): bool
    {
        return file_exists(self::langFilePath(self::normalizeCode($locale)));
    }

    public static function normalizeCode(string $locale): string
    {
        $locale = str_replace('_', '-', trim($locale));
        if (str_contains($locale, '-')) {
            $parts = explode('-', $locale, 2);

            return strtolower($parts[0]) . '-' . strtoupper($parts[1]);
        }

        return strtolower($locale);
    }

    /** ISO 639-1 base code for a BCP 47 tag (en-US → en), or null if already base-only. */
    public static function getParentLocale(string $locale): ?string
    {
        $locale = self::normalizeCode($locale);
        if (!str_contains($locale, '-')) {
            return null;
        }

        return explode('-', $locale, 2)[0];
    }

    public static function isRegionalVariant(string $locale): bool
    {
        return self::getParentLocale($locale) !== null;
    }

    /** @return list<string> normalized regional codes for a base language */
    public static function getRegionalVariantsForBase(string $baseLocale): array
    {
        $baseLocale = self::normalizeCode($baseLocale);
        $map = include __DIR__ . '/data/regional-locales.php';
        $regions = $map[$baseLocale] ?? [];
        $variants = [];
        foreach ($regions as $region) {
            $variants[] = self::normalizeCode($baseLocale . '-' . $region);
        }

        return $variants;
    }

    /** @return list<string> all configured regional locale codes */
    public static function getConfiguredRegionalLocales(): array
    {
        $map = include __DIR__ . '/data/regional-locales.php';
        $locales = [];
        foreach ($map as $base => $regions) {
            foreach ($regions as $region) {
                $locales[] = self::normalizeCode($base . '-' . $region);
            }
        }

        sort($locales);

        return $locales;
    }

    /**
     * Deep-merge locale data: parent first, child overrides (meta merged).
     *
     * @param array<string, mixed> $base
     * @param array<string, mixed> $override
     * @return array<string, mixed>
     */
    public static function mergeLocaleData(array $base, array $override): array
    {
        $baseMeta = is_array($base['meta'] ?? null) ? $base['meta'] : [];
        $overrideMeta = is_array($override['meta'] ?? null) ? $override['meta'] : [];
        unset($base['meta'], $override['meta']);

        $merged = array_replace_recursive($base, $override);
        if ($baseMeta !== [] || $overrideMeta !== []) {
            $merged['meta'] = array_replace($baseMeta, $overrideMeta);
        }

        return $merged;
    }

    public static function langFilePath(string $locale): string
    {
        return self::getLangDirectory() . '/' . self::normalizeCode($locale) . '.php';
    }

    public static function loadLocaleFile(string $locale): ?array
    {
        $locale = self::normalizeCode($locale);
        $path = self::langFilePath($locale);
        if (!file_exists($path)) {
            return null;
        }

        $data = include $path;
        if (!is_array($data)) {
            return null;
        }

        $parent = self::getParentLocale($locale);
        if ($parent === null || !self::hasLocale($parent)) {
            return $data;
        }

        $base = self::loadLocaleFile($parent);
        if ($base === null) {
            return $data;
        }

        return self::mergeLocaleData($base, $data);
    }

    public static function getLocaleMeta(string $locale): array
    {
        $data = self::loadLocaleFile($locale);

        return is_array($data['meta'] ?? null) ? $data['meta'] : [];
    }

    public static function getStrategyName(string $locale): string
    {
        return StrategyResolver::strategyName($locale, self::getLocaleMeta($locale));
    }

    public static function getTranslations(string $locale): ?array
    {
        $data = self::loadLocaleFile($locale);

        return $data === null ? null : self::stripMeta($data);
    }

    public static function validateTranslations(array $translations): array
    {
        $missing = [];
        foreach (self::REQUIRED_KEYS as $key) {
            if (empty($translations[$key])) {
                $missing[] = $key;
            }
        }

        return $missing;
    }

    public static function getExtensionKeys(): array
    {
        return self::EXTENSION_KEYS;
    }

    public static function validateExtensionTranslations(array $translations): array
    {
        $missing = [];
        foreach (self::EXTENSION_KEYS as $key) {
            if (!isset($translations[$key]) || $translations[$key] === '') {
                $missing[] = $key;
                continue;
            }
            if (in_array($key, ['ordinals', 'currencies', 'units', 'weekdays'], true) && !is_array($translations[$key])) {
                $missing[] = $key . ' (must be array)';
            }
        }

        if (isset($translations['weekdays']) && is_array($translations['weekdays'])) {
            $missing = array_merge($missing, WeekdayHelper::missingKeys($translations['weekdays']));
        }

        return $missing;
    }

    private static function stripMeta(array $data): array
    {
        unset($data['meta']);

        return $data;
    }
}
