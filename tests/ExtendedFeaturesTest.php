<?php

namespace Pino\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pino\Numera;

class ExtendedFeaturesTest extends TestCase
{
    #[DataProvider('negativeNumberProvider')]
    public function testNegativeNumbers(string $locale, int|float|string $input, string $expected): void
    {
        $this->assertSame($expected, Numera::init($locale)->n2w($input));
    }

    public static function negativeNumberProvider(): array
    {
        return [
            ['en', -500, 'negative five hundred'],
            ['fa', -500, 'منفی پانصد'],
            ['en', '-42', 'negative forty-two'],
        ];
    }

    #[DataProvider('decimalNumberProvider')]
    public function testDecimalNumbers(string $locale, int|float|string $input, string $expected): void
    {
        $this->assertSame($expected, Numera::init($locale)->n2w($input));
    }

    public static function decimalNumberProvider(): array
    {
        return [
            ['en', 3.14, 'three point one four'],
            ['en', '3.14', 'three point one four'],
            ['en', 1250.75, 'one thousand, two hundred fifty point seven five'],
            ['en', '1,250.75', 'one thousand, two hundred fifty point seven five'],
            ['fa', 3.14, 'سه ممیز یک چهار'],
            ['fa', '2,5', 'دو ممیز پنج'],
        ];
    }

    #[DataProvider('ordinalProvider')]
    public function testToOrdinal(string $locale, int $number, string $expected): void
    {
        $numera = Numera::init($locale);
        $this->assertSame($expected, $numera->toOrdinal($number));
        $this->assertSame($expected, $numera->n2o($number));
    }

    public static function ordinalProvider(): array
    {
        return [
            ['en', 1, 'first'],
            ['en', 3, 'third'],
            ['en', 21, 'twenty-first'],
            ['en', 100, 'one hundredth'],
            ['en', 11, 'eleventh'],
            ['fa', 1, 'اول'],
            ['fa', 3, 'سوم'],
            ['fa', 21, 'بیست و یکم'],
            ['fa', 100, 'صدم'],
        ];
    }

    #[DataProvider('currencyProvider')]
    public function testToCurrency(string $locale, int|float|string $amount, string $currency, string $expected): void
    {
        $this->assertSame($expected, Numera::init($locale)->toCurrency($amount, $currency));
    }

    public static function currencyProvider(): array
    {
        return [
            [
                'en',
                1250.50,
                'USD',
                'one thousand, two hundred fifty dollars and fifty cents',
            ],
            [
                'en',
                3.01,
                'GBP',
                'three pounds and one penny',
            ],
            [
                'fa',
                150000,
                'IRR',
                'صد و پنجاه هزار ریال',
            ],
        ];
    }

    #[DataProvider('unitProvider')]
    public function testWithUnit(string $locale, int|float $number, string $unit, string $expected): void
    {
        $this->assertSame($expected, Numera::init($locale)->withUnit($number, $unit));
    }

    public static function unitProvider(): array
    {
        return [
            ['en', 1, 'kg', 'one kilogram'],
            ['en', 3, 'kg', 'three kilograms'],
            ['en', 1, 'hour', 'one hour'],
            ['en', 5, 'hour', 'five hours'],
            ['fa', 1, 'day', 'یک روز'],
            ['fa', 5, 'day', 'پنج روز'],
        ];
    }

    public function testSetCamelCaseChainsWithNewFeatures(): void
    {
        $result = Numera::init('en')
            ->setCamelCase(true)
            ->n2w(-10);

        $this->assertSame('Negative Ten', $result);
    }

    public function testLocaleSwitchingPreservesExtendedFeatures(): void
    {
        $numera = Numera::init('en');
        $this->assertSame('negative one', $numera->n2w(-1));

        $numera->setLocale('fa');
        $this->assertSame('منفی یک', $numera->n2w(-1));
        $this->assertSame('اول', $numera->toOrdinal(1));
    }

    public function testZeroDecimal(): void
    {
        $this->assertSame('zero point five', Numera::init('en')->n2w('0.5'));
    }

    public function testUnsupportedCurrencyThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Numera::init('en')->toCurrency(10, 'XYZ');
    }

    public function testUnsupportedUnitThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Numera::init('en')->withUnit(10, 'lightyear');
    }

    #[DataProvider('weekdayProvider')]
    public function testToWeekday(string $locale, int|string $day, string $expected): void
    {
        $numera = Numera::init($locale);
        $this->assertSame($expected, $numera->toWeekday($day));
        $this->assertSame($expected, $numera->d2w($day));
    }

    public static function weekdayProvider(): array
    {
        return [
            ['en', 'monday', 'monday'],
            ['en', 1, 'monday'],
            ['en', 0, 'sunday'],
            ['fa', 'saturday', 'شنبه'],
            ['fa', 6, 'شنبه'],
            ['fa', 5, 'جمعه'],
            ['fa', 'friday', 'جمعه'],
        ];
    }

    public function testGetWeekdaysEnglishOrder(): void
    {
        $this->assertSame(
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            array_keys(Numera::init('en')->getWeekdays())
        );
    }

    public function testGetWeekdaysPersianStartsSaturday(): void
    {
        $this->assertSame(
            ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            array_keys(Numera::init('fa')->getWeekdays())
        );
        $this->assertSame(
            ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه'],
            array_values(Numera::init('fa')->getWeekdays())
        );
    }

    public function testInvalidWeekdayThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Numera::init('en')->toWeekday('notaday');
    }
}
