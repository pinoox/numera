<?php
/**
 *      ****  *  *     *  ****  ****  *    *
 *      *  *  *  * *   *  *  *  *  *   *  *
 *      ****  *  *  *  *  *  *  *  *    *
 *      *     *  *   * *  *  *  *  *   *  *
 *      *     *  *    **  ****  ****  *    *
 * @author   Pinoox
 * @link https://www.pinoox.com/
 * @license  https://opensource.org/licenses/MIT MIT License
 */

namespace Pino\Tests;

use PHPUnit\Framework\TestCase;
use Pino\Numera;

class NumeraTest extends TestCase
{
    public function testConstruct()
    {
        $number = new Numera('en');
        $this->assertInstanceOf(Numera::class, $number);
        $this->assertEquals('en', $number->getLocale());
        $this->assertEquals('en', $number->getLocaleFallback());
    }

    public function testInit()
    {
        $number = Numera::init('en');
        $this->assertInstanceOf(Numera::class, $number);
        $this->assertEquals('en', $number->getLocale());
        $this->assertEquals('en', $number->getLocaleFallback());
    }

    public function testConvertNumberToWords()
    {
        $number = Numera::init('en');
        $result = $number->convertNumberToWords('4,454,545,156');
        $this->assertEquals('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six', $result);
    }

    public function testN2w()
    {
        $number = Numera::init('en');
        $result = $number->convertNumberToWords('4,454,545,156');
        $this->assertEquals('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six', $result);
    }

    public function testConvertNumberToWordsWithCamelCase()
    {
        $number = Numera::init('en');
        $result = $number->setCamelCase(true)->convertNumberToWords(4454545156);
        $this->assertEquals('Four Billion, Four Hundred Fifty-Four Million, Five Hundred Forty-Five Thousand, One Hundred Fifty-Six', $result);
    }

    public function testW2n()
    {
        $number = Numera::init('en');
        $result = $number->w2n('four billion, four hundred fifty-four million, five hundred forty-five thousand, one hundred fifty-six');
        $this->assertEquals(4454545156, $result);
    }

    public function testConvertWordsToNumberWithCameCase()
    {
        $number = Numera::init('en');
        $result = $number->setCamelCase(true)->convertWordsToNumber('Four Billion, Four Hundred Fifty-Four Million, Five Hundred Forty-Five Thousand, One Hundred Fifty-Six');
        $this->assertEquals(4454545156, $result);
    }

    public function testSetCamelCase()
    {
        $number = Numera::init('en');
        $number->setCamelCase(true);
        $this->assertTrue($number->hasCamelCase());
    }

    public function testSetLocale()
    {
        $number = Numera::init('en');
        $number->setLocale('fa');
        $this->assertEquals('fa', $number->getLocale());
    }

    public function testSetLocaleFallback()
    {
        $number = Numera::init('en');
        $number->setLocaleFallback('fa');
        $this->assertEquals('fa', $number->getLocaleFallback());
    }

    public function testGetTranslates()
    {
        $number = Numera::init('en');
        $translates = $number->getLocaleTranslates();
        $this->assertIsArray($translates);
        $this->assertNotEmpty($translates);
    }

    public function testAddTranslate()
    {
        $number = Numera::init('en');
        $number->addTranslate('fr', ['hello' => 'bonjour']);
        $translates = $number->getTranslates();
        $this->assertArrayHasKey('fr', $translates);
        $this->assertEquals('bonjour', $translates['fr']['hello']);
    }
}
