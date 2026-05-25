<?php

namespace Pino\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pino\LocaleRegistry;
use Pino\Numera;
use Pino\Strategy\StrategyResolver;

class LocaleCoverageTest extends TestCase
{
    public function testAllIso639LocalesAreRegistered(): void
    {
        $iso = array_keys(LocaleRegistry::getIso639_1Codes());
        $available = LocaleRegistry::getAvailableLocales();

        foreach ($iso as $code) {
            $this->assertContains($code, $available, "Missing lang file for ISO 639-1 code: {$code}");
        }

        $this->assertGreaterThanOrEqual(count($iso), count($available));
    }

    public function testGermanLocaleDeclaresGermanStrategy(): void
    {
        $this->assertSame('german', LocaleRegistry::getStrategyName('de'));
        $this->assertSame('french', LocaleRegistry::getStrategyName('fr'));
        $this->assertSame('multiplier_tens', LocaleRegistry::getStrategyName('id'));
        $this->assertSame('default', LocaleRegistry::getStrategyName('en'));
    }

    public function testNumeraUsesMetaStrategy(): void
    {
        $this->assertSame('german', Numera::init('de')->getStrategyName());
        $this->assertSame('default', Numera::init('en')->getStrategyName());
    }

    #[DataProvider('localeProvider')]
    public function testLocaleHasRequiredTranslationKeys(string $locale): void
    {
        $translations = LocaleRegistry::getTranslations($locale);
        $this->assertNotNull($translations, "No translations for {$locale}");

        $missing = LocaleRegistry::validateTranslations($translations);
        $this->assertSame([], $missing, "Locale {$locale} missing keys: " . implode(', ', $missing));
    }

    #[DataProvider('localeProvider')]
    public function testLocaleHasExtensionKeysInLangFile(string $locale): void
    {
        $translations = LocaleRegistry::getTranslations($locale);
        $this->assertNotNull($translations, "No translations for {$locale}");

        $missing = LocaleRegistry::validateExtensionTranslations($translations);
        $this->assertSame(
            [],
            $missing,
            "Locale {$locale} missing extension keys in src/lang/{$locale}.php: " . implode(', ', $missing)
        );
    }

    #[DataProvider('localeProvider')]
    public function testLocaleProducesNonEmptyWords(string $locale): void
    {
        if (StrategyResolver::usesCustomNumberStrategy($locale, LocaleRegistry::getLocaleMeta($locale))) {
            $this->assertNotEmpty(Numera::init($locale)->n2w(42));
            return;
        }

        $numera = Numera::init($locale);
        $this->assertNotEmpty($numera->n2w(0));
        $this->assertNotEmpty($numera->n2w(1));
        $this->assertNotEmpty($numera->n2w(21));
        $this->assertNotEmpty($numera->n2w(100));
        $this->assertNotEmpty($numera->n2w(1001));
    }

    #[DataProvider('defaultRoundTripProvider')]
    public function testDefaultStrategyRoundTrip(string $locale, int $number): void
    {
        if (StrategyResolver::usesCustomNumberStrategy($locale, LocaleRegistry::getLocaleMeta($locale))) {
            $this->markTestSkipped('Locale uses a dedicated number strategy.');
        }

        $numera = Numera::init($locale);
        $words = $numera->n2w($number);
        $this->assertSame($number, $numera->w2n($words));
    }

    public static function localeProvider(): array
    {
        return array_map(fn($code) => [$code], LocaleRegistry::getAvailableLocales());
    }

    public static function defaultRoundTripProvider(): array
    {
        $samples = [5, 21, 1000, 12345];
        $locales = ['en', 'es', 'fr', 'it', 'nl', 'ru', 'fa', 'tr', 'pt', 'pl', 'sv'];
        $data = [];
        foreach ($locales as $locale) {
            foreach ($samples as $number) {
                $data[] = [$locale, $number];
            }
        }

        return $data;
    }
}
