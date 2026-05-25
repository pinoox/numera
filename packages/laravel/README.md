# pinoox/numera-laravel

Laravel integration for [pinoox/numera](https://github.com/pinoox/numera) — number-to-words conversion with Facade and config.

## Requirements

- PHP ^8.0
- Laravel 9, 10, or 11 (`illuminate/support` ^9|^10|^11)
- `pinoox/numera` ^2.1

## Installation

```bash
composer require pinoox/numera
composer require pinoox/numera-laravel
```

Laravel auto-discovers `NumeraServiceProvider` and registers the `Numera` facade alias.

### Publish configuration

```bash
php artisan vendor:publish --tag=numera-config
```

`config/numera.php`:

```php
return [
    'default_locale' => 'en',
    'fallback_locale' => 'en',
];
```

## Usage

```php
use Pinoox\Numera\Laravel\Facades\Numera;

// Cardinal
echo Numera::n2w(2024);

// New in 2.1
echo Numera::toYear(2024);
echo Numera::toFraction(1.5);
echo Numera::toPhone('+98 21 8834');
echo Numera::toIp('192.168.1.1');
echo Numera::toVersion('2.14.0');

// Locale at runtime
Numera::setLocale('fa');
echo Numera::n2w(-500);
```

All public methods on `Pino\Numera` are available through the facade.

## Container binding

The core instance is registered as a singleton under the `numera` key:

```php
$numera = app('numera');
// or
$numera = app(\Pino\Numera::class); // not auto-aliased; use 'numera'
```

## Monorepo development

When hacking on both packages from the main [numera](https://github.com/pinoox/numera) repository, `composer.json` here uses a path repository to `../../`. Run tests:

```bash
cd packages/laravel
composer install
vendor/bin/phpunit
```

## License

MIT — same as pinoox/numera.
