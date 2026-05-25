<?php

namespace Pino\Strategy;

use Pino\Numera;

interface NumberStrategyInterface
{
    public function convertToWords(Numera $numera, int $num): string;

    public function convertToSummary(Numera $numera, int|string $num): string;

    public function convertToNumber(Numera $numera, string $words, string|array|null $separators = null): int;
}
