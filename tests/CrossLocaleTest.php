<?php

namespace Pino\Tests;

use PHPUnit\Framework\TestCase;
use Pino\Numera;
use Pino\Support\LocaleTranslator;

class CrossLocaleTest extends TestCase
{
    public function testPersianNumberToEnglish(): void
    {
        $result = Numera::init('fa')->translateTo('en', 'دویست و یک');
        $this->assertSame('two hundred one', $result);
    }

    public function testEnglishNumberToPersian(): void
    {
        $result = Numera::init('en')->translateTo('fa', 'two hundred one');
        $this->assertSame('دویست و یک', $result);
    }

    public function testPersianWeekdayToEnglish(): void
    {
        $this->assertSame('saturday', Numera::init('fa')->translateTo('en', 'شنبه'));
    }

    public function testEnglishWeekdayToPersian(): void
    {
        $this->assertSame('دوشنبه', Numera::init('en')->translateTo('fa', 'monday'));
    }

    public function testStaticTranslatorBetween(): void
    {
        $translator = LocaleTranslator::between('fa', 'en');
        $this->assertSame('saturday', $translator->translate('شنبه'));
        $this->assertSame('two hundred one', $translator->translate('دویست و یک'));
    }

    public function testT2tAlias(): void
    {
        $this->assertSame(
            'دویست و یک',
            Numera::init('en')->t2t('two hundred one', 'fa')
        );
    }

    public function testNegativeNumberCrossLocale(): void
    {
        $this->assertSame(
            'negative five hundred',
            Numera::init('fa')->translateTo('en', 'منفی پانصد')
        );
    }

    public function testDecimalCrossLocale(): void
    {
        $this->assertSame(
            'three point one four',
            Numera::init('fa')->translateTo('en', 'سه ممیز یک چهار')
        );
    }

    public function testGermanToEnglish(): void
    {
        $deWords = Numera::init('de')->n2w(42);
        $this->assertSame('forty-two', Numera::init('de')->translateTo('en', $deWords));
    }

    public function testSameLocaleReturnsInput(): void
    {
        $this->assertSame('شنبه', Numera::init('fa')->translateTo('fa', 'شنبه'));
    }

    public function testUnknownTargetLocaleThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Numera::init('fa')->translateTo('xx-YY', 'یک');
    }

    public function testWeekdayKeyFromWord(): void
    {
        $fa = Numera::init('fa');
        $this->assertSame('saturday', $fa->weekdayKeyFromWord('شنبه'));
        $this->assertSame('monday', Numera::init('en')->weekdayKeyFromWord('monday'));
    }
}
