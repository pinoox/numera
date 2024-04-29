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

class Numera
{
    private $translations;
    private $dataNumbers;
    private $localeFallback;
    private string $locale;

    private bool $isCamelCase = false;

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
        if (isset($this->translations[$locale]))
            return;

        $path = __DIR__ . "/lang/{$locale}.php";
        $this->addTranslateFile($locale, $path);
    }

    public function addTranslateFile($locale, string $path): void
    {
        if (file_exists($path)) {
            $this->translations[$locale] = include $path;
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

    protected function dataNumber($key)
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

    protected function translate($key, $default = null)
    {
        $translations = $this->getLocaleTranslates();
        if (isset($translations[$key])) {
            $str = $translations[$key];
        } else if (!str_contains($key, '_')) {
            $str = $default;
        } else {
            $subs = array_map(fn($sub) => $this->translate($sub, $default), explode('_', $key));
            $str = implode('{hundred_prefix}', $subs);
        }
        if (!empty($str))
            return $this->hasCamelCase() ? ucfirst($str) : $str;
        else
            return $str;
    }

    public function convertToWords($num)
    {
        $num = str_replace(',', '', $num);
        $num = intval(trim($num));
        $units = $this->dataNumber('units');
        $teens = $this->dataNumber('teens');
        $tens = $this->dataNumber('tens');
        $hundreds = $this->dataNumber('hundreds');
        $thousands = $this->dataNumber('thousands');

        if ($num < 10) {
            return $this->translate($units[$num]);
        } elseif ($num < 20) {
            return $this->translate($teens[$num - 10]);
        } elseif ($num < 100) {
            return $this->translate($tens[(int)($num / 10)]) . ($num % 10 !== 0 ? '{ten}' . $this->translate($units[$num % 10]) : '');
        } elseif ($num < 1000) {
            return $this->translate($hundreds[(int)($num / 100)]) . ($num % 100 !== 0 ? '{hundred}' . $this->convertNumberToWords($num % 100) : '');
        } else {
            $result = '';
            for ($i = 0; $num > 0; $i++) {
                $part = $num % 1000;
                if ($part !== 0) {
                    $result = $this->convertNumberToWords($part) . '{thousand}' . $this->translate($thousands[$i]) . ($result ? '{part}' : '') . $result;
                }
                $num = (int)($num / 1000);
            }

            return $this->replaceSeparator($result);
        }
    }

    public function convertToSummary($num): string
    {
        $thousands = $this->dataNumber('thousands');
        $num = str_replace(',', '', $num);
        $num = number_format($num);
        $parts = explode(',', $num);
        $count = count($parts) - 1;
        $result = '';
        foreach ($parts as $i => $part) {
            $result .= $i === 0 ? $part . '{thousand}' . $this->translate($thousands[$count - $i]) : '{part}' . $part . '{thousand}' . $this->translate($thousands[$count - $i]);
        }

        return $this->replaceSeparator($result);
    }

    protected function getSeparators(): array
    {
        $between = $this->translate('between');
        return [
            'thousand' => $this->translate('between.thousand', ' '),
            'ten' => $this->translate('between.ten', '-'),
            'hundred' => $this->translate('between.hundred', $between ?? ' '),
            'part' => $this->translate('between.part', $between ?? ', '),
            'hundred_prefix' => $this->translate('between.hundred.prefix', ' '),
        ];
    }

    protected function replaceSeparator($word)
    {
        $separators = $this->getSeparators();
        $patterns = [];
        foreach ($separators as $separator => $value) {
            $patterns[] = '/(\{' . $separator . '\})/';
        }

        return trim(preg_replace($patterns, $separators, $word));
    }

    protected function getArrayBySeparator($word, string|array|null $separators = null): array
    {
        if (!empty($separators))
            $separators = is_array($separators) ? $separators : [$separators];
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
        $translations = $this->getLocaleTranslates();
        $translations = array_flip($translations);
        $wordsArray = $this->getArrayBySeparator($words, $separators);
        $number = 0;
        $numbers = $this->dataNumber('numbers');
        $tempNumber = 0;

        foreach ($wordsArray as $word) {
            $word = strtolower($word);

            if (is_numeric($word)) {
                $num = intval($word);
            } else {
                if (!isset($translations[strtolower($word)]))
                    continue;

                $word = $translations[$word];

                if (!isset($numbers[$word]))
                    continue;

                $num = intval($numbers[$word]);
            }


            if ($word === 'hundred') {
                $tempNumber = $tempNumber > 0 ? $tempNumber : 1;
                $tempNumber = $tempNumber * $num;

            } else if (in_array($word, ['thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion'])) {
                $tempNumber = $tempNumber > 0 ? $tempNumber : 1;
                $tempNumber = $tempNumber * $num;
                $number += $tempNumber;
                $tempNumber = 0;
            } else {
                $tempNumber += $num;
            }
        }

        $number += $tempNumber;

        return $number;
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

        return $this;
    }

}