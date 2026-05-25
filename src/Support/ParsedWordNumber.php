<?php

namespace Pino\Support;

/** Canonical numeric value parsed from spoken/written words in a locale. */
final class ParsedWordNumber
{
    public function __construct(
        public readonly int $integerPart,
        public readonly string $decimalDigits = '',
        public readonly bool $isNegative = false,
    ) {
    }

    public function hasDecimal(): bool
    {
        return $this->decimalDigits !== '';
    }

    /** Value suitable for Numera::convertToWords / NumberInputParser. */
    public function toNumericValue(): int|float|string
    {
        if (!$this->hasDecimal()) {
            return $this->isNegative ? -$this->integerPart : $this->integerPart;
        }

        $sign = $this->isNegative ? '-' : '';

        return $sign . $this->integerPart . '.' . $this->decimalDigits;
    }
}
