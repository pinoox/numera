<?php
/**
 *      ****  *  *     *  ****  ****  *    *
 *      *  *  *  * *   *  *  *  *  *   *  *
 *      ****  *  *  *  *  *  *  *  *    *
 *      *     *  *   * *  *  *  *  *   *  *
 *      *     *  *    **  ****  ****  *    *
 * @author   Pinoox
 * @link https://www.pinoox.com/
 * @license  https://opensource.org/licenses/MIT MIT License
 */


namespace Pino;

use Pino\Strategy\NumberStrategyInterface;
use Pino\Strategy\StrategyResolver;
use Pino\Support\LocaleTranslator;
use Pino\Support\NumberInputParser;
use Pino\Support\WordPhraseParser;
use Pino\Support\WeekdayHelper;

class Numera
{
    private $translations;
    private $dataNumbers;
    private $localeFallback;
    private string $locale;

    private bool $isCamelCase = false;

    private NumberStrategyInterface $numberStrategy;

    /** @var array<string, array<string, mixed>> */
    private array $localeMeta = [];

    public function __construct($locale = 'en', $localeFallback = 'en')
    {
        $this->setLocale($locale);
        $this->setLocaleFallback($localeFallback);
        $this->dataNumbers = $this->loadData('numbers');
    }

    public static function init($lang = 'en', $defaultLang = 'en'): static
    {
        return new static($lang, $defaultLang);
    }

    protected function loadTranslations($locale): void
    {
        if (isset($this->translations[$locale])) {
            return;
        }

        $locale = LocaleRegistry::normalizeCode($locale);
        $data = LocaleRegistry::loadLocaleFile($locale);
        if ($data !== null) {
            $this->applyLocaleData($locale, $data);
        }
    }

    public function addTranslateFile($locale, string $file): void
    {
        if (!file_exists($file)) {
            return;
        }

        $locale = LocaleRegistry::normalizeCode($locale);
        $data = LocaleRegistry::loadLocaleFile($locale);
        if ($data === null) {
            $raw = include $file;
            if (!is_array($raw)) {
                return;
            }
            $this->applyLocaleData($locale, $raw);
            return;
        }

        $this->applyLocaleData($locale, $data);
    }

    /** @param array<string, mixed> $data */
    private function applyLocaleData(string $locale, array $data): void
    {
        $this->localeMeta[$locale] = is_array($data['meta'] ?? null) ? $data['meta'] : [];
        unset($data['meta']);
        $this->translations[$locale] = $data;
    }

    public function getParentLocale(?string $locale = null): ?string
    {
        return LocaleRegistry::getParentLocale($locale ?? $this->locale);
    }

    public function getLocaleMeta(?string $locale = null): array
    {
        $locale = $locale ?? $this->locale;

        return $this->localeMeta[$locale] ?? [];
    }

    public function getStrategyName(?string $locale = null): string
    {
        return StrategyResolver::strategyName(
            $locale ?? $this->locale,
            $this->getLocaleMeta($locale)
        );
    }

    public static function getAvailableLocales(): array
    {
        return LocaleRegistry::getAvailableLocales();
    }

    public static function hasLocale(string $locale): bool
    {
        return LocaleRegistry::hasLocale($locale);
    }

    public function addTranslate($locale, array $translates): void
    {
        $meta = [];
        if (isset($translates['meta']) && is_array($translates['meta'])) {
            $meta = $translates['meta'];
            unset($translates['meta']);
        }

        $this->localeMeta[$locale] = $meta;
        $this->translations[$locale] = $translates;
    }

    protected function loadData($name)
    {
        return include __DIR__ . "/data/{$name}.php";
    }

    public function dataNumber($key)
    {
        return $this->dataNumbers[$key] ?? null;
    }

    public function getTranslates(): array
    {
        return $this->translations;
    }

    public function getLocaleTranslates(): array
    {
        return $this->translations[$this->locale] ?? $this->translations[$this->localeFallback];
    }

    public function translate($key, $default = null, ?bool $camelCase = null)
    {
        $str = $this->resolveTranslation($key, $default);
        if ($str === null || $str === '') {
            return $str ?? '';
        }

        $useCamelCase = $camelCase ?? $this->hasCamelCase();

        return $useCamelCase ? ucfirst($str) : $str;
    }

    public function translateMap(string $key, array $default = []): array
    {
        $value = $this->resolveNestedTranslation($this->getLocaleTranslates(), $key);
        if (is_array($value)) {
            return $value;
        }

        $fallbackTranslations = $this->translations[$this->localeFallback] ?? [];
        $fallback = $this->resolveNestedTranslation($fallbackTranslations, $key);

        return is_array($fallback) ? $fallback : $default;
    }

    private function resolveNestedTranslation(array $translations, string $key): mixed
    {
        if (array_key_exists($key, $translations)) {
            return $translations[$key];
        }

        if (!str_contains($key, '.')) {
            return null;
        }

        $value = $translations;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    private function resolveTranslation(string $key, mixed $default): mixed
    {
        $translations = $this->getLocaleTranslates();
        if (array_key_exists($key, $translations)) {
            $value = $translations[$key];
            if (is_array($value)) {
                return $value;
            }

            return (string)$value;
        }

        if (!str_contains($key, '_')) {
            if ($default !== null) {
                return $default;
            }

            $fallback = $this->translations[$this->localeFallback][$key] ?? null;

            return $fallback !== null ? (string)$fallback : '';
        }

        $subs = array_map(
            fn($sub) => (string)$this->translate($sub, is_string($default) ? $default : ' ', camelCase: false),
            explode('_', $key)
        );

        return implode('{hundred_prefix}', $subs);
    }

    public function applyCamelCase(string $text): string
    {
        if (!$this->hasCamelCase()) {
            return $text;
        }

        $parts = explode(' ', $text);

        return implode(' ', array_map(fn($part) => ucfirst($part), $parts));
    }

    public function convertToWords(int|float|string $num): string
    {
        $parsed = NumberInputParser::parse($num);
        $parts = [];

        if ($parsed->isNegative && $parsed->integerPart === 0 && !$parsed->hasDecimal()) {
            $parts[] = $this->translate('negative', 'negative', camelCase: false);
            $parts[] = $this->numberStrategy->convertToWords($this, 0);

            return $this->applyCamelCase(implode(' ', array_filter($parts)));
        }

        if ($parsed->isNegative) {
            $parts[] = $this->translate('negative', 'negative', camelCase: false);
        }

        $parts[] = $this->numberStrategy->convertToWords($this, $parsed->integerPart);

        if ($parsed->hasDecimal()) {
            $parts[] = $this->translate('point', 'point', camelCase: false);
            $parts[] = $this->convertDecimalDigitsToWords($parsed->decimalDigits);
        }

        return $this->applyCamelCase(implode(' ', array_filter($parts)));
    }

    public function toOrdinal(int $number): string
    {
        return (new OrdinalConverter($this))->toOrdinal($number);
    }

    public function n2o(int $number): string
    {
        return $this->toOrdinal($number);
    }

    public function toCurrency(int|float|string $amount, string $currency = 'USD'): string
    {
        $parsed = NumberInputParser::parse($amount);
        $config = CurrencyConfig::get($currency);
        $currencyWords = $this->translateMap('currencies.' . strtoupper($currency), $config);

        $mainAmount = $parsed->integerPart;
        $subAmount = 0;

        if ($config['has_sub_unit'] && $parsed->hasDecimal()) {
            $fraction = str_pad($parsed->decimalDigits, $config['decimals'], '0', STR_PAD_RIGHT);
            $fraction = substr($fraction, 0, $config['decimals']);
            $subAmount = (int)$fraction;
        }

        $segments = [];

        if ($parsed->isNegative) {
            $segments[] = $this->translate('negative', 'negative', camelCase: false);
        }

        $mainLabel = $this->currencyLabel(
            $config,
            $currencyWords,
            'main',
            $mainAmount === 1 && $subAmount === 0
        );
        $segments[] = $this->numberStrategy->convertToWords($this, $mainAmount) . ' ' . $mainLabel;

        if ($config['has_sub_unit'] && $subAmount > 0) {
            $and = $this->translate('and', 'and', camelCase: false);
            $subLabel = $this->currencyLabel($config, $currencyWords, 'sub', $subAmount === 1);
            $segments[] = $and;
            $segments[] = $this->numberStrategy->convertToWords($this, $subAmount) . ' ' . $subLabel;
        }

        return $this->applyCamelCase(implode(' ', array_filter($segments)));
    }

    public function withUnit(int|float|string $number, string $unit): string
    {
        $parsed = NumberInputParser::parse($number);
        $definition = UnitConfig::get($unit);
        $unitWords = $this->translateMap('units.' . strtolower($unit), []);

        $segments = [];

        if ($parsed->isNegative) {
            $segments[] = $this->translate('negative', 'negative', camelCase: false);
        }

        $amount = $parsed->integerPart;
        if ($parsed->hasDecimal()) {
            $segments[] = $this->convertToWords($number);
            $segments[] = $this->unitLabel($definition, $unitWords, false);
        } else {
            $useSingular = $amount === 1;
            $segments[] = $this->numberStrategy->convertToWords($this, $amount);
            $segments[] = $this->unitLabel($definition, $unitWords, $useSingular);
        }

        return $this->applyCamelCase(implode(' ', array_filter($segments)));
    }

    public function toWeekday(int|string $day): string
    {
        $key = WeekdayHelper::resolveKey($day);
        $weekdays = $this->translateMap('weekdays', []);
        if (empty($weekdays[$key])) {
            throw new \InvalidArgumentException("Weekday not defined for locale: {$key}");
        }

        return $this->applyCamelCase((string)$weekdays[$key]);
    }

    public function d2w(int|string $day): string
    {
        return $this->toWeekday($day);
    }

    /**
     * Resolve a localized weekday name to the canonical key (monday, saturday, …).
     */
    public function weekdayKeyFromWord(string $word): ?string
    {
        return WordPhraseParser::weekdayKeyFromWord($this, $word);
    }

    /**
     * Translate spoken/written words from one locale to another (number or weekday).
     *
     * Pipeline: source words → number/weekday key → target words.
     */
    public function translateTo(string $targetLocale, string $words, ?string $sourceLocale = null): string
    {
        $from = LocaleRegistry::normalizeCode($sourceLocale ?? $this->locale);
        $to = LocaleRegistry::normalizeCode($targetLocale);

        return (new LocaleTranslator($from, $to, (string)$this->localeFallback))->translate($words);
    }

    /** Alias for {@see translateTo()}. */
    public function t2t(string $words, string $targetLocale, ?string $sourceLocale = null): string
    {
        return $this->translateTo($targetLocale, $words, $sourceLocale);
    }

    public function translatorTo(string $targetLocale, ?string $sourceLocale = null): LocaleTranslator
    {
        $from = LocaleRegistry::normalizeCode($sourceLocale ?? $this->locale);
        $to = LocaleRegistry::normalizeCode($targetLocale);

        return new LocaleTranslator($from, $to, (string)$this->localeFallback);
    }

    /**
     * @return array<string, string> canonical key => localized name
     */
    public function getWeekdays(bool $ordered = true): array
    {
        $weekdays = $this->translateMap('weekdays', []);
        $missing = WeekdayHelper::missingKeys($weekdays);
        if ($missing !== []) {
            throw new \RuntimeException(
                'Locale ' . $this->getLocale() . ' missing weekday keys: ' . implode(', ', $missing)
            );
        }

        $keys = $ordered
            ? WeekdayHelper::orderedKeys($this->getLocaleMeta())
            : WeekdayHelper::KEYS;

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->applyCamelCase((string)$weekdays[$key]);
        }

        return $result;
    }

    private function convertDecimalDigitsToWords(string $digits): string
    {
        $units = $this->dataNumber('units');
        $separator = $this->translate('between.decimal', ' ', camelCase: false);
        $words = [];

        foreach (str_split($digits) as $digit) {
            if (!ctype_digit($digit)) {
                continue;
            }
            $words[] = $this->translate($units[(int)$digit], camelCase: false);
        }

        return implode($separator === '' ? ' ' : $separator, $words);
    }

    private function currencyLabel(array $config, array $overrides, string $part, bool $singular): string
    {
        $key = $singular ? "{$part}_singular" : "{$part}_plural";
        if (isset($overrides[$key])) {
            return (string)$overrides[$key];
        }
        if (isset($overrides[$part])) {
            return (string)$overrides[$part];
        }

        return (string)$config[$key];
    }

    private function unitLabel(array $definition, array $overrides, bool $singular): string
    {
        if ($this->getLocale() === 'fa' || str_starts_with($this->getLocale(), 'fa-')) {
            return (string)($overrides['singular'] ?? $overrides['form'] ?? $definition['singular']);
        }

        $key = $singular ? 'singular' : 'plural';

        return (string)($overrides[$key] ?? $definition[$key]);
    }

    public function convertToSummary($num): string
    {
        return $this->numberStrategy->convertToSummary($this, $num);
    }

    protected function getSeparators(): array
    {
        $between = $this->translate('between', camelCase: false);
        return [
            'thousand' => $this->translate('between.thousand', ' ', camelCase: false),
            'ten' => $this->translate('between.ten', '-', camelCase: false),
            'hundred' => $this->translate('between.hundred', $between ?? ' ', camelCase: false),
            'part' => $this->translate('between.part', $between ?? ', ', camelCase: false),
            'hundred_prefix' => $this->translate('between.hundred.prefix', ' ', camelCase: false),
        ];
    }

    public function replaceSeparator($word)
    {
        $separators = $this->getSeparators();
        $patterns = [];
        foreach ($separators as $separator => $value) {
            $patterns[] = '/(\{' . $separator . '\})/';
        }

        return trim(preg_replace($patterns, array_values($separators), $word));
    }

    public function getArrayBySeparator($word, string|array|null $separators = null): array
    {
        if (!empty($separators)) {
            $separators = is_array($separators) ? $separators : [$separators];
        }
        $separators = array_merge(
            (array)$separators,
            $this->getSeparators(),
            [' ', ', ']
        );

        usort($separators, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        return explode('|', str_replace(array_values($separators), '|', $word));
    }

    public function w2n($words, string|array|null $separators = null)
    {
        return $this->convertToNumber($words, $separators);
    }

    public function s2n($words, string|array|null $separators = null)
    {
        return $this->convertToNumber($words, $separators);
    }

    public function n2w($num)
    {
        return $this->convertToWords($num);
    }

    public function n2s($num)
    {
        return $this->convertToSummary($num);
    }

    public function convertToNumber($words, string|array|null $separators = null)
    {
        return $this->numberStrategy->convertToNumber($this, $words, $separators);
    }

    private function normalizeInput(int|float|string $num): int
    {
        return NumberInputParser::parse($num)->integerPart;
    }

    private function resolveNumberStrategy(string $locale): NumberStrategyInterface
    {
        return StrategyResolver::resolve($locale, $this->getLocaleMeta($locale));
    }

    /**
     * @return bool
     */
    public function hasCamelCase(): bool
    {
        return $this->isCamelCase;
    }

    /**
     * @param bool $isCamelCase
     * @return $this
     */
    public function setCamelCase(bool $isCamelCase): static
    {
        $this->isCamelCase = $isCamelCase;

        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getLocaleFallback(): mixed
    {
        return $this->localeFallback;
    }


    /**
     * @param mixed $localeFallback
     * @return $this
     */
    public function setLocaleFallback(mixed $localeFallback): static
    {
        $this->localeFallback = $localeFallback;
        $this->loadTranslations($localeFallback);
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;
        $this->loadTranslations($locale);
        $this->numberStrategy = $this->resolveNumberStrategy($locale);

        return $this;
    }

}
