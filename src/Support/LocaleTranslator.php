<?php

namespace Pino\Support;

use Pino\LocaleRegistry;
use Pino\Numera;

/**
 * Cross-locale translation via a language-neutral intermediate (number or weekday key).
 */
class LocaleTranslator
{
    private string $sourceLocale;
    private string $targetLocale;
    private string $fallbackLocale;

    public function __construct(
        string $sourceLocale,
        string $targetLocale,
        string $fallbackLocale = 'en',
    ) {
        $this->sourceLocale = LocaleRegistry::normalizeCode($sourceLocale);
        $this->targetLocale = LocaleRegistry::normalizeCode($targetLocale);
        $this->fallbackLocale = LocaleRegistry::normalizeCode($fallbackLocale);
    }

    public static function between(string $from, string $to, string $fallback = 'en'): self
    {
        return new self($from, $to, $fallback);
    }

    public function getSourceLocale(): string
    {
        return $this->sourceLocale;
    }

    public function getTargetLocale(): string
    {
        return $this->targetLocale;
    }

    public function translate(string $words): string
    {
        $words = trim($words);
        if ($words === '') {
            return '';
        }

        $this->assertLocale($this->sourceLocale, 'source');
        $this->assertLocale($this->targetLocale, 'target');

        if ($this->sourceLocale === $this->targetLocale) {
            return $words;
        }

        $source = Numera::init($this->sourceLocale, $this->fallbackLocale);
        $target = Numera::init($this->targetLocale, $this->fallbackLocale);

        $weekdayKey = WordPhraseParser::weekdayKeyFromWord($source, $words);
        if ($weekdayKey !== null) {
            return $target->toWeekday($weekdayKey);
        }

        $parsed = WordPhraseParser::parseNumber($source, $words);

        return $target->convertToWords($parsed->toNumericValue());
    }

    private function assertLocale(string $locale, string $role): void
    {
        if (!LocaleRegistry::hasLocale($locale)) {
            throw new \InvalidArgumentException(
                "Unknown {$role} locale \"{$locale}\". Add src/lang/{$locale}.php or a regional variant."
            );
        }
    }
}
