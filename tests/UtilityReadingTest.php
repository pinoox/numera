<?php

namespace Pino\Tests;

use PHPUnit\Framework\TestCase;
use Pino\Numera;

class UtilityReadingTest extends TestCase
{
    public function testToIp(): void
    {
        $result = Numera::init('en')->toIp('192.168.1.1');
        $this->assertStringContainsString('ninety-two', $result);
        $this->assertStringContainsString('sixty-eight', $result);
        $this->assertStringContainsString('dot', $result);
    }

    public function testToVersionSemver(): void
    {
        $this->assertSame(
            'two point fourteen point zero',
            Numera::init('en')->toVersion('2.14.0')
        );
    }

    public function testToVersionWithPrerelease(): void
    {
        $result = Numera::init('en')->toVersion('1.0.0-beta');
        $this->assertStringContainsString('one', $result);
        $this->assertStringContainsString('dash', $result);
        $this->assertStringContainsString('beta', $result);
    }

    public function testInvalidIp(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Numera::init('en')->toIp('not-an-ip');
    }

    public function testAliasN2ip(): void
    {
        $n = Numera::init('en');
        $this->assertSame($n->toIp('192.168.1.1'), $n->n2ip('192.168.1.1'));
    }
}
