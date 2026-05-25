<?php
/**
 * @author   Pinoox
 * @license  https://opensource.org/licenses/MIT MIT License
 */

namespace Pino\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pino\Numera;

class GermanTest extends TestCase
{
    private function german(): Numera
    {
        return Numera::init('de');
    }

    public function testConvertLargeNumberToWords(): void
    {
        $result = $this->german()->convertToWords(4_454_545_156);
        $this->assertEquals(
            'vier Milliarden vierhundertvierundfünfzig Millionen fünfhundertfünfundvierzigtausendeinhundertsechsundfünfzig',
            $result
        );
    }

    #[DataProvider('germanNumericInputProvider')]
    public function testConvertLargeNumberToWordsWithDotSeparator(int|string $input): void
    {
        $result = $this->german()->convertToWords($input);
        $this->assertEquals(
            'vier Milliarden vierhundertvierundfünfzig Millionen fünfhundertfünfundvierzigtausendeinhundertsechsundfünfzig',
            $result
        );
    }

    public static function germanNumericInputProvider(): array
    {
        return [
            [4_454_545_156],
            ['4.454.545.156'],
        ];
    }

    public function testConvertLargeNumberToWordsCamelCase(): void
    {
        $result = $this->german()->setCamelCase(true)->convertToWords(4_454_545_156);
        $this->assertEquals(
            'Vier Milliarden Vierhundertvierundfünfzig Millionen Fünfhundertfünfundvierzigtausendeinhundertsechsundfünfzig',
            $result
        );
    }

    #[DataProvider('germanNumericInputProvider')]
    public function testConvertLargeNumberToSummary(int|string $input): void
    {
        $result = $this->german()->convertToSummary($input);
        $this->assertEquals(
            '4 Milliarden, 454 Millionen, 545 Tausend, 156',
            $result
        );
    }

    public function testGermanTeensAndTens(): void
    {
        $n = $this->german();
        $this->assertEquals('vierundfünfzig', $n->n2w(54));
        $this->assertEquals('einundzwanzig', $n->n2w(21));
        $this->assertEquals('zwanzig', $n->n2w(20));
    }

    public function testGermanThousandsConcatenation(): void
    {
        $n = $this->german();
        $this->assertEquals('viertausend', $n->n2w(4000));
        $this->assertEquals('eintausend', $n->n2w(1000));
    }

    public function testGermanHundredForms(): void
    {
        $n = $this->german();
        $this->assertEquals('eins', $n->n2w(1));
        $this->assertEquals('einhundert', $n->n2w(100));
        $this->assertEquals('fünfhundertfünfundvierzigtausendeinhundertsechsundfünfzig', $n->n2w(545156));
    }

    public function testConvertWordsToNumber(): void
    {
        $result = $this->german()->convertToNumber(
            'vier Milliarden vierhundertvierundfünfzig Millionen fünfhundertfünfundvierzigtausendeinhundertsechsundfünfzig'
        );
        $this->assertEquals(4_454_545_156, $result);
    }

    public function testConvertWordsToNumberCamelCase(): void
    {
        $result = $this->german()->setCamelCase(true)->convertToNumber(
            'Vier Milliarden Vierhundertvierundfünfzig Millionen Fünfhundertfünfundvierzigtausendeinhundertsechsundfünfzig'
        );
        $this->assertEquals(4_454_545_156, $result);
    }
}
