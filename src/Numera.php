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

use Pino\Strategy\DefaultNumberStrategy;
use Pino\Strategy\GermanNumberStrategy;
use Pino\Strategy\NumberStrategyInterface;

class Numera
{
    private $translations;
    private $dataNumbers;
    private $localeFallback;
    private string $locale;

    private bool $isCamelCase = false;

    private NumberStrategyInterface $numberStrategy;

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

        $path = __DIR__ . "/lang/{$locale}.php";
        $this->addTranslateFile($locale, $path);
    }

    public function addTranslateFile($locale, string $file): void
    {
        if (file_exists($file)) {
            $this->translations[$locale] = include $file;
        }
    }

    public function addTranslate($locale, array $translates): void
    {
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
        $translations = $this->getLocaleTranslates();
        if (isset($translations[$key])) {
            $str = $translations[$key];
        } elseif (!str_contains($key, '_')) {
            $str = $default;
        } else {
            $subs = array_map(fn($sub) => $this->translate($sub, $default, $camelCase), explode('_', $key));
            $str = implode('{hundred_prefix}', $subs);
        }
        if (empty($str)) {
            return $str;
        }

        $useCamelCase = $camelCase ?? $this->hasCamelCase();

        return $useCamelCase ? ucfirst($str) : $str;
    }

    public function applyCamelCase(string $text): string
    {
        if (!$this->hasCamelCase()) {
            return $text;
        }

        $parts = explode(' ', $text);

        return implode(' ', array_map(fn($part) => ucfirst($part), $parts));
    }

    public function convertToWords($num)
    {
        $num = $this->normalizeInput($num);

        return $this->numberStrategy->convertToWords($this, $num);
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

        return trim(preg_replace($patterns, $separators, $word));
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

    private function normalizeInput($num): int
    {
        $num = str_replace(['.', ',', ' '], '', (string)$num);

        return (int)trim($num);
    }

    private function resolveNumberStrategy(string $locale): NumberStrategyInterface
    {
        $germanLocales = ['de', 'de-de', 'de-DE'];
        if (in_array(strtolower($locale), array_map('strtolower', $germanLocales), true)
            || str_starts_with(strtolower($locale), 'de')) {
            return new GermanNumberStrategy();
        }

        return new DefaultNumberStrategy();
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
