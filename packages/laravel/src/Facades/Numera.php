<?php

namespace Pinoox\Numera\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Pino\Numera setLocale(string $locale)
 * @method static \Pino\Numera setLocaleFallback(mixed $localeFallback)
 * @method static string n2w(int|float|string $num)
 * @method static int|float w2n($words, string|array|null $separators = null)
 * @method static string n2s($num)
 * @method static string n2o(int $number)
 * @method static string toOrdinal(int $number)
 * @method static string toCurrency(int|float|string $amount, string $currency = 'USD')
 * @method static string withUnit(int|float|string $number, string $unit)
 * @method static string toWeekday(int|string $day)
 * @method static string translateTo(string $targetLocale, string $words, ?string $sourceLocale = null)
 * @method static string t2t(string $words, string $targetLocale, ?string $sourceLocale = null)
 * @method static string toFraction(float $number)
 * @method static string n2f(float $number)
 * @method static string toYear(int $year)
 * @method static string n2y(int $year)
 * @method static string toPhone(string $phone)
 * @method static string n2p(string $phone)
 * @method static string toRoman(int $number)
 * @method static string n2r(int $number)
 * @method static string toIp(string $ip)
 * @method static string n2ip(string $ip)
 * @method static string toVersion(string $version)
 * @method static \Pino\Numera setCamelCase(bool $isCamelCase)
 *
 * @see \Pino\Numera
 */
class Numera extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'numera';
    }
}
