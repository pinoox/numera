<?php

namespace Pino\Tests;

use PHPUnit\Framework\TestCase;
use Pino\Numera;

class FractionTest extends TestCase
{
    public function testEnglishHalf(): void
    {
        $this->assertSame('one half', Numera::init('en')->toFraction(0.5));
    }

    public function testEnglishQuarter(): void
    {
        $this->assertSame('one quarter', Numera::init('en')->toFraction(0.25));
    }

    public function testEnglishThreeQuarters(): void
    {
        $this->assertSame('three quarters', Numera::init('en')->toFraction(0.75));
    }

    public function testEnglishOneAndHalf(): void
    {
        $result = Numera::init('en')->toFraction(1.5);
        $this->assertStringContainsString('one', $result);
        $this->assertStringContainsString('half', $result);
    }

    public function testEnglishTwoAndThird(): void
    {
        $result = Numera::init('en')->toFraction(2 + 1 / 3);
        $this->assertStringContainsString('two', $result);
        $this->assertStringContainsString('third', $result);
    }

    public function testPersianHalf(): void
    {
        $this->assertSame('نیم', Numera::init('fa')->toFraction(0.5));
    }

    public function testPersianQuarter(): void
    {
        $this->assertSame('یک چهارم', Numera::init('fa')->toFraction(0.25));
    }

    public function testAliasN2f(): void
    {
        $this->assertSame(Numera::init('en')->toFraction(0.5), Numera::init('en')->n2f(0.5));
    }
}
