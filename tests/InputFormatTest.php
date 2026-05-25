<?php

namespace Pino\Tests;

use PHPUnit\Framework\TestCase;
use Pino\Numera;

class InputFormatTest extends TestCase
{
    public function testDetectFormatEuropean(): void
    {
        $this->assertSame('european', Numera::detectFormat('1.234.567,89'));
    }

    public function testDetectFormatSwiss(): void
    {
        $this->assertSame('swiss', Numera::detectFormat("1'234'567.89"));
    }

    public function testDetectFormatPersianDigits(): void
    {
        $this->assertSame('persian', Numera::detectFormat('۱۲۳۴'));
    }

    public function testDetectFormatUnderscore(): void
    {
        $this->assertSame('underscore', Numera::detectFormat('1_000_000'));
    }

    public function testDetectFormatDefault(): void
    {
        $this->assertSame('default', Numera::detectFormat('1234.56'));
    }

    public function testEuropeanFormatN2w(): void
    {
        $n = Numera::init('en');
        $this->assertStringContainsString('million', $n->n2w('1.234.567,89'));
    }

    public function testSwissFormatN2w(): void
    {
        $n = Numera::init('en');
        $words = $n->n2w("1'234'567.89");
        $this->assertStringContainsString('million', strtolower($words));
    }

    public function testPersianDigitsN2w(): void
    {
        $n = Numera::init('en');
        $words = strtolower(str_replace(',', '', $n->n2w('۱۲۳۴')));
        $this->assertStringContainsString('one thousand', $words);
        $this->assertStringContainsString('thirty-four', $words);
    }

    public function testUnderscoreFormatN2w(): void
    {
        $n = Numera::init('en');
        $this->assertStringContainsString('million', $n->n2w('1_000_000'));
    }
}
