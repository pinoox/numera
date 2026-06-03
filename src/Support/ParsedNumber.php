<?php

namespace Pino\Support;

final class ParsedNumber
{
    public function __construct(
        public bool $isNegative,
        public int $integerPart,
        public string $decimalDigits,
    ) {
    }

    public function hasDecimal(): bool
    {
        return $this->decimalDigits !== '';
    }
}
