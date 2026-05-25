<?php

namespace Pino\Support;

final class ParsedNumber
{
    public function __construct(
        public readonly bool $isNegative,
        public readonly int $integerPart,
        public readonly string $decimalDigits,
    ) {
    }

    public function hasDecimal(): bool
    {
        return $this->decimalDigits !== '';
    }
}
