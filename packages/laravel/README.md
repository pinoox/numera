# pinoox/numera-laravel

Laravel integration for [pinoox/numera](https://github.com/pinoox/numera).

## Who should use this?

| Project type | Install |
|--------------|---------|
| **Laravel 9 / 10 / 11** | `composer require pinoox/numera-laravel` |
| Plain PHP, other frameworks | [`pinoox/numera`](https://github.com/pinoox/numera) only — see main [README](../../README.md) |

`pinoox/numera-laravel` depends on `pinoox/numera`; you do **not** need a separate `composer require pinoox/numera` in Laravel apps.

## Requirements

- PHP ^8.0
- Laravel 9, 10, or 11
- `illuminate/support` ^9.0 | ^10.0 | ^11.0

## Installation

```bash
composer require pinoox/numera-laravel
```

Laravel auto-discovers:

- `Pinoox\Numera\Laravel\NumeraServiceProvider`
- Facade alias `Numera` → `Pinoox\Numera\Laravel\Facades\Numera`

## Configuration

Publish config once:

```bash
php artisan vendor:publish --tag=numera-config
```

`config/numera.php`:

| Key | Description |
|-----|-------------|
| `default_locale` | Locale on boot (e.g. `en`, `fa`, `fa-IR`) |
| `fallback_locale` | Used when a translation key is missing |

```php
return [
    'default_locale' => 'en',
    'fallback_locale' => 'en',
];
```

## Usage

```php
use Pinoox\Numera\Laravel\Facades\Numera;

echo Numera::n2w(2024);
echo Numera::toYear(2024);
echo Numera::toCurrency(1500, 'USD');

Numera::setLocale('fa');
echo Numera::n2w('۱۲۳۴');
```

Every public method on `Pino\Numera` is available on the facade (`n2w`, `w2n`, `toOrdinal`, `toFraction`, `toPhone`, `toIp`, …).

## Service container

Singleton binding key: `numera`

```php
/** @var \Pino\Numera $numera */
$numera = app('numera');
```

## Core documentation

Locale list, regional variants, strategies, and advanced APIs:

- [Main README / API reference](https://github.com/pinoox/numera/blob/master/README.md)
- [CHANGELOG](https://github.com/pinoox/numera/blob/master/CHANGELOG.md)

## Developing in the monorepo

From the [numera](https://github.com/pinoox/numera) repository root:

```bash
cd packages/laravel
composer install
vendor/bin/phpunit
```

## License

MIT — same as [pinoox/numera](https://github.com/pinoox/numera).
