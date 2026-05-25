<?php

namespace Pino\Strategy;

use Pino\Numera;

/** Forwards n2w / summary to {@see DefaultNumberStrategy}; subclasses override w2n. */
abstract class DelegatesDefaultNumberStrategy implements NumberStrategyInterface
{
    private ?DefaultNumberStrategy $default = null;

    protected function defaultStrategy(): DefaultNumberStrategy
    {
        return $this->default ??= new DefaultNumberStrategy();
    }

    public function convertToWords(Numera $numera, int $num): string
    {
        return $this->defaultStrategy()->convertToWords($numera, $num);
    }

    public function convertToSummary(Numera $numera, int|string $num): string
    {
        return $this->defaultStrategy()->convertToSummary($numera, $num);
    }
}
