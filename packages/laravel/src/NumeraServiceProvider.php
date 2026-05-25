<?php

namespace Pinoox\Numera\Laravel;

use Illuminate\Support\ServiceProvider;
use Pino\Numera as NumeraCore;

class NumeraServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/numera.php', 'numera');

        $this->app->singleton('numera', function ($app) {
            $locale = (string)$app['config']->get('numera.default_locale', 'en');
            $fallback = (string)$app['config']->get('numera.fallback_locale', 'en');

            return NumeraCore::init($locale, $fallback);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/numera.php' => config_path('numera.php'),
            ], 'numera-config');
        }

        $locale = (string)$this->app['config']->get('numera.default_locale', 'en');
        $fallback = (string)$this->app['config']->get('numera.fallback_locale', 'en');

        /** @var NumeraCore $numera */
        $numera = $this->app->make('numera');
        $numera->setLocale($locale);
        $numera->setLocaleFallback($fallback);
    }
}
