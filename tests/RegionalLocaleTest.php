<?php

namespace Pino\Tests;

use PHPUnit\Framework\TestCase;
use Pino\LocaleRegistry;
use Pino\Numera;
use Pino\Strategy\StrategyResolver;

class RegionalLocaleTest extends TestCase
{
    public function testRegionalLocaleInheritsBaseTranslations(): void
    {
        $base = LocaleRegistry::getTranslations('en');
        $us = LocaleRegistry::getTranslations('en-US');

        $this->assertNotNull($base);
        $this->assertNotNull($us);
        $this->assertSame($base['one'], $us['one']);
        $this->assertSame($base['hundred'], $us['hundred']);
    }

    public function testRegionalLocaleMeta(): void
    {
        $meta = LocaleRegistry::getLocaleMeta('en-US');
        $this->assertSame('US', $meta['region']);
        $this->assertSame('en', $meta['parent']);
    }

    public function testGermanRegionalVariantsUseGermanStrategy(): void
    {
        foreach (['de-DE', 'de-AT', 'de-CH'] as $locale) {
            $this->assertTrue(LocaleRegistry::hasLocale($locale), $locale);
            $this->assertSame('german', LocaleRegistry::getStrategyName($locale), $locale);
            $this->assertSame('german', Numera::init($locale)->getStrategyName(), $locale);
            $this->assertNotEmpty(Numera::init($locale)->n2w(42));
        }
    }

    public function testFaIrInheritsPersianWeekOrder(): void
    {
        $this->assertSame(
            ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            array_keys(Numera::init('fa-IR')->getWeekdays())
        );
        $this->assertSame('شنبه', Numera::init('fa-IR')->toWeekday('saturday'));
    }

    public function testEnGbCurrencyOverride(): void
    {
        $result = Numera::init('en-GB')->toCurrency(3.01, 'GBP');
        $this->assertStringContainsString('pound', $result);
    }

    public function testNumeraParentLocaleHelper(): void
    {
        $numera = Numera::init('en-US');
        $this->assertSame('en', $numera->getParentLocale());
        $this->assertNull(Numera::init('en')->getParentLocale());
    }

    public function testConfiguredRegionalLocalesHaveFiles(): void
    {
        foreach (LocaleRegistry::getConfiguredRegionalLocales() as $code) {
            $this->assertTrue(
                LocaleRegistry::hasLocale($code),
                "Missing regional lang file: {$code}.php"
            );
        }
    }

    public function testUnknownStrategyFallsBackToDefault(): void
    {
        $meta = ['strategy' => 'nonexistent'];
        $this->assertSame(StrategyResolver::DEFAULT, StrategyResolver::strategyName('xx', $meta));
    }
}
