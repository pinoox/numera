<?php

namespace Pino\Tests;

use PHPUnit\Framework\TestCase;
use Pino\Numera;

class RomanTest extends TestCase
{
    public function testToRomanEdgeCases(): void
    {
        $this->assertSame('IV', Numera::init('en')->toRoman(4));
        $this->assertSame('IX', Numera::init('en')->toRoman(9));
        $this->assertSame('XIV', Numera::init('en')->toRoman(14));
        $this->assertSame('XL', Numera::init('en')->toRoman(40));
        $this->assertSame('XC', Numera::init('en')->toRoman(90));
        $this->assertSame('CD', Numera::init('en')->toRoman(400));
        $this->assertSame('CM', Numera::init('en')->toRoman(900));
        $this->assertSame('MMMCMXCIX', Numera::init('en')->toRoman(3999));
    }

    public function testFromRomanCaseInsensitive(): void
    {
        $this->assertSame(4, Numera::fromRoman('iv'));
        $this->assertSame(9, Numera::fromRoman('Ix'));
        $this->assertSame(3999, Numera::fromRoman('mmmcmxCix'));
    }

    public function testToRomanOutOfRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Numera::init('en')->toRoman(4000);
    }

    public function testAliasN2r(): void
    {
        $n = Numera::init('en');
        $this->assertSame($n->toRoman(14), $n->n2r(14));
    }
}
