<?php

require_once __DIR__ . '/vendor/autoload.php';

use Pino\Numera;

echo Numera::init('fa')->setCamelCase(true)->convertNumberToWords('12,254,128');