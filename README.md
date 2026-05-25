**Numera: A PHP Library for Number to Words Conversion**
=====================================================

[![Coverage](https://codecov.io/gh/pinoox/numera/branch/master/graph/badge.svg)](https://codecov.io/gh/pinoox/numera)
[![Latest Stable Version](https://poser.pugx.org/pinoox/numera/v/stable)](https://packagist.org/packages/pinoox/numera)
[![GitHub Stars](https://img.shields.io/github/stars/pinoox/numera.svg)](https://github.com/pinoox/numera/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/pinoox/numera.svg)](https://github.com/pinoox/numera/network)
[![GitHub Issues](https://img.shields.io/github/issues/pinoox/numera.svg)](https://github.com/pinoox/numera/issues)
[![License](https://img.shields.io/github/license/pinoox/numera.svg)](https://github.com/pinoox/numera/blob/master/LICENSE)
[![Total Downloads](https://poser.pugx.org/pinoox/numera/downloads)](https://packagist.org/packages/pinoox/numera)

Numera is a PHP library that converts numbers to words (and back), with support for **184 ISO 639-1 languages**, regional variants, ordinals, currency, units, weekdays, cross-locale translation, and specialized reading modes (fractions, years, phone numbers, Roman numerals, IP/version strings).

**Requirements:** PHP ^8.0, Composer. No runtime dependencies.

**Quick start:**

```php
use Pino\Numera;

echo Numera::init('en')->n2w(1234);
echo Numera::init('fa')->n2w('۱۲۳۴');
echo Numera::init('en')->toYear(2024);
```

Install: `composer require pinoox/numera` — see [CHANGELOG.md](CHANGELOG.md) for release notes.

* [Features](#features)
* [API reference](#api-reference)
* [Project layout](#project-layout)
* [Installation and Setup](#installation-and-setup)
    * [Install via Composer](#install-via-composer)
    * [Initialize Numera](#initialize-numera)
* [Usage](#usage)
    * [Convert Numbers to Words](#convert-numbers-to-words)
    * [Negative Numbers](#negative-numbers)
    * [Decimal Numbers](#decimal-numbers)
    * [Ordinal Numbers](#ordinal-numbers)
    * [Currency Formatting](#currency-formatting)
    * [Units](#units)
    * [Convert Numbers to Summary Words](#convert-numbers-to-summary)
    * [Convert Words to Numbers](#convert-words-to-numbers)
    * [Use Camel Case](#use-camel-case)
    * [Input Formats](#input-formats)
    * [Fractions](#fractions)
    * [Years](#years)
    * [Phone Numbers](#phone-numbers)
    * [Roman Numerals](#roman-numerals)
    * [IP and Version Strings](#ip-and-version-strings)
    * [Laravel Integration](#laravel-integration)
* [Supported Languages](#supported-languages)
* [Set Locale](#set-locale)
* [Set Locale Fallback](#set-locale-fallback)
* [Get Translates](#get-translates)
* [Add Translate](#add-translate)
* [Create a New Language](#create-a-new-language)
* [Author](#author)
* [Contributing](#contributing)
* [Documentation](#documentation)
* [License](#license)

**Features**
------------

* Convert numbers to words (e.g. 1234 to "one thousand two hundred thirty-four")
* Convert words to numbers (e.g. "one thousand two hundred thirty-four" to 1234)
* Negative numbers (`n2w(-500)` → "negative five hundred")
* Decimal numbers (`n2w(3.14)` → "three point one four")
* Ordinal numbers (`toOrdinal(21)` / `n2o(21)` → "twenty-first")
* Currency formatting (`toCurrency(1250.50, 'USD')`)
* Units with singular/plural (`withUnit(3, 'kg')` → "three kilograms")
* Weekday names (`toWeekday(1)` / `getWeekdays()` — Monday=1 ISO, or `saturday`, …)
* Cross-locale word translation (`translateTo('en', 'دویست و یک')` / `t2t()`)
* European, Swiss, Persian-digit, and underscore numeric input formats
* Fractions (`toFraction()` / `n2f()`), year reading (`toYear()` / `n2y()`), phone numbers (`toPhone()` / `n2p()`)
* Roman numerals (`toRoman()` / `n2r()`, `Numera::fromRoman()`)
* IPv4 and semver reading (`toIp()` / `n2ip()`, `toVersion()`)
* Laravel package [`pinoox/numera-laravel`](packages/laravel/)
* 180+ ISO 639-1 language packs (English and Persian fully extended)
* Camel case support for output words
* Easy to use and extend
* GitHub Actions CI (PHP 8.0–8.3) and optional Codecov badge


**API reference**
-----------------

| Method | Alias | Description |
|--------|-------|-------------|
| `n2w($num)` | — | Number → words (supports multiple input formats) |
| `w2n($words)` | `s2n` | Words → number |
| `n2s($num)` | — | Number → summary (e.g. 4 Billion, 454 Million) |
| `toOrdinal($n)` | `n2o` | Ordinal words |
| `toCurrency($amount, $code)` | — | Money with main/sub units |
| `withUnit($n, $unit)` | — | Amount + unit (kg, hour, …) |
| `toWeekday($day)` | `d2w` | ISO key, index, or name → weekday word |
| `getWeekdays()` | — | All weekdays (order from `meta.week_starts_on`) |
| `translateTo($locale, $words)` | `t2t` | Cross-locale word translation |
| `detectFormat($input)` | — | Static: `default`, `european`, `swiss`, `persian`, `underscore` |
| `toFraction($float)` | `n2f` | Common fractions (half, quarter, …) |
| `toYear($year)` | `n2y` | Year reading (English pairs; else cardinal) |
| `toPhone($string)` | `n2p` | Phone digit-by-digit |
| `toRoman($int)` | `n2r` | Integer → Roman (1–3999) |
| `fromRoman($roman)` | — | Static: Roman → integer |
| `toIp($ipv4)` | `n2ip` | IPv4 octets as words |
| `toVersion($semver)` | — | Version string (digits + labels) |
| `setLocale($code)` | — | Switch locale (loads `src/lang/{code}.php`) |
| `setLocaleFallback($code)` | — | Fallback when a key is missing |
| `setCamelCase($bool)` | — | Title-case output |

Static helpers: `Numera::init($locale, $fallback)`, `getAvailableLocales()`, `hasLocale($code)`.


**Project layout**
------------------

```
src/
  Numera.php              # Main API
  LocaleRegistry.php      # Locale load/merge (regional variants)
  Strategy/               # german, french, multiplier_tens, default
  Support/                # Input parsers, fractions, phone, IP, …
  Languages/              # English-specific year rules
  lang/                   # Per-locale translations (184+ files)
  data/                   # numbers.php, regional-locales, …
packages/laravel/         # pinoox/numera-laravel (monorepo)
tests/                    # PHPUnit (1000+ tests)
```


**Installation and Setup**
-------------------------

### Install via Composer

You can install Numera using Composer:
```
composer require pinoox/numera
```

### Initialize Numera

To use Numera, you need to initialize it with a locale. You can do this using the `init` method:
```php
use Pino\Numera;

$numera = Numera::init('en'); // Initialize with English locale
```

### Convert Numbers to Words

To convert a number to words, use the `convertToWords` method:
```php
$result = $numera->convertToWords(4454545156);
echo $result; // Output: "four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six"
```

Alternatively, you can use the `n2w` method for a simpler syntax:
```php
$result = $numera->n2w('4,454,545,156');
echo $result; // Output: "four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six"
```

### Negative Numbers

```php
echo Numera::init('en')->n2w(-500);  // negative five hundred
echo Numera::init('fa')->n2w(-500);  // منفی پانصد
```

### Decimal Numbers

Each digit after the decimal separator is read individually. Accepts floats or strings (`3.14`, `1,250.75`).

```php
echo Numera::init('en')->n2w(3.14);       // three point one four
echo Numera::init('en')->n2w('1,250.75'); // one thousand, two hundred fifty point seven five
echo Numera::init('fa')->n2w(3.14);       // سه ممیز یک چهار
```

### Ordinal Numbers

```php
$numera = Numera::init('en');
echo $numera->toOrdinal(21);  // twenty-first
echo $numera->n2o(100);       // one hundredth

$numera->setLocale('fa');
echo $numera->toOrdinal(3);   // سوم
echo $numera->toOrdinal(21);  // بیست و یکم
```

### Currency Formatting

Supported codes: `USD`, `EUR`, `GBP`, `IRR`, `IRT` (extend via language file `currencies` maps).

```php
echo Numera::init('en')->toCurrency(1250.50, 'USD');
// one thousand, two hundred fifty dollars and fifty cents

echo Numera::init('fa')->toCurrency(150000, 'IRR');
// صد و پنجاه هزار ریال

echo Numera::init('en')->toCurrency(3.01, 'GBP');
// three pounds and one penny
```

### Units

Supported units: `kg`, `g`, `km`, `m`, `cm`, `hour`, `minute`, `second`, `day`, `week`, `month`, `year`.

```php
echo Numera::init('en')->withUnit(1, 'kg');   // one kilogram
echo Numera::init('en')->withUnit(5, 'hour'); // five hours
echo Numera::init('fa')->withUnit(1, 'day');  // یک روز
```

### Weekdays

Day names live in each language file under `weekdays` (`monday` … `sunday`). Use ISO weekday numbers (1 = Monday … 7 = Sunday), PHP `date('w')` (0 = Sunday … 6 = Saturday), or the English key name.

```php
echo Numera::init('en')->toWeekday(1);        // monday
echo Numera::init('en')->toWeekday('friday'); // friday
echo Numera::init('fa')->toWeekday(5);        // جمعه (ISO: Friday)
echo Numera::init('fa')->toWeekday(6);        // شنبه (ISO: Saturday)
echo Numera::init('fa')->d2w('saturday');     // شنبه

// All days; Persian locale starts on Saturday (meta.week_starts_on)
print_r(Numera::init('fa')->getWeekdays());
```

Optional `meta.week_starts_on` in `src/lang/{locale}.php` controls the order returned by `getWeekdays()` (default: `monday`).

### Cross-locale translation (words → words)

Convert spoken/written text in one language to another via a neutral intermediate (integer or weekday key):

```php
// Persian → English
echo Numera::init('fa')->translateTo('en', 'دویست و یک');  // two hundred one
echo Numera::init('fa')->translateTo('en', 'شنبه');       // saturday

// English → Persian
echo Numera::init('en')->translateTo('fa', 'two hundred one'); // دویست و یک
echo Numera::init('en')->t2t('monday', 'fa');                 // دوشنبه

LocaleTranslator::between('fa', 'en')->translate('منفی پانصد'); // negative five hundred
```

Supports cardinal numbers (including negative and decimal phrases), and weekday names. Currency/unit phrases are not converted yet.

Chain with locale and camel case:

```php
echo Numera::init('en')->setCamelCase(true)->n2w(-10); // Negative Ten
```

### Convert Numbers to Summary

To convert a number to summary words, use the `convertToSummary` method:
```php
$result = $numera->convertToSummary(4454545156);
echo $result; // Output: "4 Billion, 454 Million, 545 Thousand, 156"
```

Alternatively, you can use the `n2w` method for a simpler syntax:
```php
$result = $numera->n2s('4,454,545,156');
echo $result; // Output: "4 Billion, 454 Million, 545 Thousand, 156"
```
### Convert Words to Numbers

To convert words to a number, use the `convertToNumber` method:
```php
$result = $numera->convertToNumber('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six');
echo $result; // Output: 4454545156
```

Alternatively, you can use the `w2n` method for a simpler syntax:
```php
$result = $numera->w2n("4 Billion, 454 Million, 545 Thousand, 156");
echo $result; // Output: 4454545156
```

You can also specify separators for the `w2n` method:
```php
$result = $numera->w2n('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six', [' ', ',']);
echo $result; // Output: 4454545156
```

### Use Camel Case

To use camel case for output words, use the `setCamelCase` method:
```php
$numera->setCamelCase(true);
$result = $numera->convertToWords('4,454,545,156');
echo $result; // Output: "Four Billion, Four Hundred Fifty-Four Million, Five Hundred Forty-Five Thousand, One Hundred Fifty-Six"
```

### Input Formats

`n2w()` and `w2n()` normalize these string formats before parsing:

| Format | Example | `detectFormat()` |
|--------|---------|------------------|
| Default | `1234.56`, `1,250.75` | `default` |
| European | `1.234.567,89` | `european` |
| Swiss | `1'234'567.89` | `swiss` |
| Persian/Arabic digits | `۱۲۳۴` | `persian` |
| Underscore | `1_000_000` | `underscore` |

```php
Numera::detectFormat("1.234.567,89"); // european
echo Numera::init('en')->n2w('۱۲۳۴');
echo Numera::init('en')->n2w("1'234'567.89");
echo Numera::init('en')->n2w('1_000_000');
```

### Fractions

```php
echo Numera::init('en')->toFraction(0.5);   // one half
echo Numera::init('en')->n2f(1.5);         // one and a half
echo Numera::init('fa')->toFraction(0.5);  // نیم
```

### Years

English uses paired year reading (1999 → nineteen ninety-nine). Other locales use cardinal form.

```php
echo Numera::init('en')->toYear(2024);  // twenty twenty-four
echo Numera::init('en')->n2y(2000);     // two thousand
echo Numera::init('fa')->toYear(1402);  // cardinal Persian form
```

### Phone Numbers

Reads digit-by-digit with locale-aware digit words.

```php
echo Numera::init('en')->toPhone('+1 415 555 0172');
echo Numera::init('en')->n2p('021-8834-1100');
```

### Roman Numerals

```php
echo Numera::init('en')->toRoman(1999);      // MCMXCIX
echo Numera::fromRoman('xiv');               // 14
```

### IP and Version Strings

```php
echo Numera::init('en')->toIp('192.168.1.1');
echo Numera::init('en')->n2ip('10.0.0.1');
echo Numera::init('en')->toVersion('2.14.0');
echo Numera::init('en')->toVersion('1.0.0-beta');
```

### Laravel Integration

The Laravel bridge lives in this monorepo under `packages/laravel/` ([full docs](packages/laravel/README.md)). On Packagist: `pinoox/numera-laravel`.

```bash
composer require pinoox/numera
composer require pinoox/numera-laravel
```

```php
use Pinoox\Numera\Laravel\Facades\Numera;

echo Numera::n2w(42);
echo Numera::toYear(2024);
echo Numera::toFraction(0.5);
```

Publish config:

```bash
php artisan vendor:publish --tag=numera-config
```

Config keys: `default_locale`, `fallback_locale` (see `packages/laravel/config/numera.php`).

### Supported Languages

Numera ships with **184 ISO 639-1** language packs under `src/lang/`, plus **regional variants** such as `en-US`, `en-GB`, `fa-IR`, `de-DE` (see `src/data/regional-locales.php`). Each base file contains cardinals, separators, and v1.2 keys (`negative`, `point`, `ordinals`, `currencies`, `units`, `weekdays`, …). Edit the locale file directly — see `src/lang/en.php` and `TranslationGuide.md`.

**Regional variants (BCP 47):** Files like `src/lang/en-US.php` only declare overrides (usually `meta.region`); translations are merged from the parent (`en.php` → `en-US.php`). Configured regions are listed in `src/data/regional-locales.php`.

```php
Numera::init('en-US')->n2w(42);       // same words as en, US meta
Numera::init('en-GB')->toCurrency(3.01, 'GBP');
Numera::init('fa-IR')->getWeekdays();  // inherits fa (week starts Saturday)
Numera::init('de-AT')->getStrategyName(); // german (inherited from de)
```

**Number strategies:** Languages with special compounding use `meta.strategy` in the lang file (see `src/data/locale-strategies.php`):

| Strategy | Locales | Reason |
|----------|---------|--------|
| `german` | `de`, `de-*` | Inverted tens (`einundzwanzig`), `tausend` concatenation |
| `french` | `fr`, `fr-*` | Vigesimal 70–99 (`soixante-dix`, `quatre-vingts`) |
| `multiplier_tens` | `id`, `ms`, `vi`, `jv`, `tl`, `mi`, `sm`, `to`, `ty` | `dua puluh tiga` = 2×10+3, not 2+10+3 |
| `default` | Most others | English-style place value |

Welsh (`cy`), Breton (`br`), and some locales with incomplete lang data may still need dedicated strategies or richer translations in `src/lang/`.

### Set Locale

To set the locale for the Numera object, use the `setLocale` method:
```php
$numera->setLocale('fa'); // Set locale to Persian
```

### Set Locale Fallback

To set the fallback locale for the Numera object, use the `setLocaleFallback` method:
```php
$numera->setLocaleFallback('en'); // Set fallback locale to English
```

### Get Translates

To get the translates for the current locale, use the `getTranslates` method:
```php
$translates = $numera->getTranslates();
print_r($translates); // Output: Array of translates for the current locale
```

### Add Translate

To add translates for a specific locale, use the `addTranslate` method:
```php
$numera->addTranslate('fr', ['four' => 'quatre']); // Add French translates
```

### Add Translate File

To add translates by array file for a specific locale, use the `addTranslateFile` method:
```php
$numera->addTranslateFile('fr','/path/lang/fr.php'); // Add French translates
```

### Create a New Language

If you want to add support for a new language, please read our [Translation Guide](TranslationGuide.md) for a step-by-step guide on how to create a new language pack.

**Author**
---------

Numera was created by [Pinoox](https://www.pinoox.com/).

**Contributing**
------------

If you'd like to contribute to Numera, please fork the repository and submit a pull request. We'd love to have your help.

**Testing**
---------

```bash
composer install
vendor/bin/phpunit
```

Laravel package tests:

```bash
cd packages/laravel && composer install && vendor/bin/phpunit
```

CI runs on push/PR to `master` (see `.github/workflows/ci.yml`).


**Upgrade from 2.0**
--------------------

```bash
composer require pinoox/numera:^2.1
```

New methods are additive; existing `n2w`, `w2n`, `toCurrency`, etc. are unchanged. Add `fractions`, `plus`, `dot`, `dash` to custom lang files if you use `toFraction`, `toPhone`, `toIp`, or `toVersion`.


**Documentation**
-------------

* [README.md](README.md) — This file
* [CHANGELOG.md](CHANGELOG.md) — Release history
* [TranslationGuide.md](TranslationGuide.md) — Creating or extending language packs
* [packages/laravel/README.md](packages/laravel/README.md) — Laravel Facade and config
* [src/lang/_schema.php](src/lang/_schema.php) — Expected locale file structure
* [src/lang/en.php](src/lang/en.php) — Reference locale (full v2.1 keys)

**License**
---------

Numera is licensed under the [MIT License](https://opensource.org/licenses/MIT). See the LICENSE file for more information. 
