<?php

namespace Pino\Tests;

use PHPUnit\Framework\TestCase;
use Pino\Numera;

class PhoneTest extends TestCase
{
    public function testInternationalPhone(): void
    {
        $result = Numera::init('en')->toPhone('+1 415 555 0172');
        $this->assertStringContainsString('plus', $result);
        $this->assertStringContainsString('four', $result);
        $this->assertStringContainsString('five', $result);
        $this->assertStringContainsString('zero', $result);
    }

    public function testLocalPhoneWithDashes(): void
    {
        $result = Numera::init('en')->toPhone('021-8834-1100');
        $this->assertStringContainsString('zero', $result);
        $this->assertStringContainsString('eight', $result);
        $this->assertStringContainsString('one', $result);
    }

    public function testPersianDigitsInPhone(): void
    {
        $result = Numera::init('en')->toPhone('۰۲۱-۸۸۳۴');
        $this->assertStringContainsString('zero', $result);
        $this->assertStringContainsString('eight', $result);
    }

    public function testAliasN2p(): void
    {
        $n = Numera::init('en');
        $phone = '021-8834';
        $this->assertSame($n->toPhone($phone), $n->n2p($phone));
    }
}
