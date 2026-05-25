<?php

namespace Pino\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pino\LocaleRegistry;
use Pino\Numera;
use Pino\Strategy\StrategyResolver;

class SpecialStrategyTest extends TestCase
{
    public function testFrenchLocaleDeclaresFrenchStrategy(): void
    {
        $this->assertSame('french', LocaleRegistry::getStrategyName('fr'));
        $this->assertSame('french', Numera::init('fr-FR')->getStrategyName());
    }

    public function testMultiplierTensLocales(): void
    {
        foreach (['id', 'ms', 'vi', 'jv'] as $locale) {
            $this->assertSame(
                StrategyResolver::MULTIPLIER_TENS,
                LocaleRegistry::getStrategyName($locale),
                $locale
            );
        }
    }

    #[DataProvider('frenchRoundTripProvider')]
    public function testFrenchRoundTrip(int $number): void
    {
        $numera = Numera::init('fr');
        $words = $numera->n2w($number);
        $this->assertSame($number, $numera->w2n($words), "Failed for {$words}");
    }

    public static function frenchRoundTripProvider(): array
    {
        return [
            [21],
            [70],
            [77],
            [80],
            [81],
            [90],
            [99],
            [201],
            [1000],
        ];
    }

    #[DataProvider('multiplierTensRoundTripProvider')]
    public function testMultiplierTensRoundTrip(string $locale, int $number): void
    {
        $numera = Numera::init($locale);
        $words = $numera->n2w($number);
        $this->assertSame($number, $numera->w2n($words), "{$locale}: {$words}");
    }

    public static function multiplierTensRoundTripProvider(): array
    {
        $locales = ['id', 'vi', 'ms'];
        $numbers = [11, 21, 50, 53, 99, 100, 201];
        $data = [];
        foreach ($locales as $locale) {
            foreach ($numbers as $number) {
                $data[] = [$locale, $number];
            }
        }

        return $data;
    }

    public function testFrenchCrossLocaleStillWorks(): void
    {
        $this->assertSame(
            'eighty',
            Numera::init('fr')->translateTo('en', 'quatre-vingts')
        );
    }

    public function testIndonesianCrossLocale(): void
    {
        $this->assertSame(
            'twenty-one',
            Numera::init('id')->translateTo('en', 'dua puluh satu')
        );
    }
}
