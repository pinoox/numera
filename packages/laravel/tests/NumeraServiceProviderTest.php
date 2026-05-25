<?php

namespace Pinoox\Numera\Laravel\Tests;

use Orchestra\Testbench\TestCase;
use Pinoox\Numera\Laravel\Facades\Numera;
use Pinoox\Numera\Laravel\NumeraServiceProvider;

class NumeraServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [NumeraServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Numera' => Numera::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('numera.default_locale', 'en');
        $app['config']->set('numera.fallback_locale', 'en');
    }

    public function testFacadeConvertsNumberToWords(): void
    {
        $this->assertSame('twenty-one', Numera::n2w(21));
    }

    public function testConfigIsPublished(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'numera-config']);

        $this->assertFileExists(config_path('numera.php'));
        $this->assertSame('en', config('numera.default_locale'));
        $this->assertSame('en', config('numera.fallback_locale'));
    }

    public function testSingletonBinding(): void
    {
        $first = $this->app->make('numera');
        $second = $this->app->make('numera');

        $this->assertSame($first, $second);
    }
}
