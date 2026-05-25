<?php

/**
 * Recommended meta.strategy per locale (also set in src/lang/{locale}.php meta).
 *
 * - german: inverted tens + concatenated thousands (de, de-*)
 * - french: vigesimal 80–99, soixante-dix 70–79 (fr, fr-*)
 * - multiplier_tens: N × ten + unit, e.g. dua puluh tiga (id, ms, vi, …)
 */
return [
    'german' => ['de'],
    'french' => ['fr'],
    'multiplier_tens' => ['id', 'ms', 'vi', 'jv', 'tl', 'mi', 'sm', 'to', 'ty'],
];
