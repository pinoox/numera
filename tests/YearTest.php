<?php

namespace Pino\Tests;

use PHPUnit\Framework\TestCase;
use Pino\Numera;

class YearTest extends TestCase
{
    public function testEnglish1999(): void
    {
        $this->assertSame('nineteen ninety-nine', Numera::init('en')->toYear(1999));
    }

    public function testEnglish2000(): void
    {
        $this->assertSame('two thousand', Numera::init('en')->toYear(2000));
    }

    public function testEnglish2005(): void
    {
        $this->assertSame('two thousand and five', Numera::init('en')->toYear(2005));
    }

    public function testEnglish2024(): void
    {
        $this->assertSame('twenty twenty-four', Numera::init('en')->toYear(2024));
    }

    public function testPersianUsesCardinal(): void
    {
        $year = 1402;
        $this->assertSame(
            Numera::init('fa')->n2w($year),
            Numera::init('fa')->toYear($year)
        );
    }

    public function testAliasN2y(): void
    {
        $n = Numera::init('en');
        $this->assertSame($n->toYear(2024), $n->n2y(2024));
    }
}
