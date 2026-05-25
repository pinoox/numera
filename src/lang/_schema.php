<?php

/**
 * Language file schema (reference only — not loaded by Numera).
 *
 * All translatable strings live in src/lang/{locale}.php.
 * Non-translatable numeric structure: src/data/numbers.php
 * Locale list: src/data/iso639-1.php
 *
 * meta.strategy (optional):
 *   - omitted or "default" → DefaultNumberStrategy
 *   - "german"            → GermanNumberStrategy (de)
 *   - "french"            → FrenchNumberStrategy (fr, vigesimal 70–99)
 *   - "multiplier_tens"   → MultiplierTensNumberStrategy (id, vi, …)
 * meta.week_starts_on (optional): monday | saturday | … — order for getWeekdays()
 * meta.parent / meta.region (optional): BCP 47 regional file (e.g. en-US.php) inherits base lang via merge
 *
 * Regional files: src/lang/{base}-{REGION}.php — only overrides + meta; base must exist (en.php, fa.php, …).
 * List: src/data/regional-locales.php
 * Weekday seeds (reference): src/data/weekday-seeds.php
 *
 * Cross-locale: Numera::translateTo($target, $words) — words → number/weekday key → target words.
 */
return [
    'meta' => ['strategy' => 'default', 'week_starts_on' => 'monday', 'parent' => 'en', 'region' => 'US'],
    // Cardinals + scales + between* — see TranslationGuide.md
    'negative' => 'negative',
    'point' => 'point',
    'and' => 'and',
    'between.decimal' => ' ',
    'ordinal_suffix' => 'th',
    'ordinal_suffixes' => ['th', 'st', 'nd', 'rd', 'th'],
    'ordinal_exceptions' => [11 => 'eleventh', 12 => 'twelfth', 13 => 'thirteenth'],
    'ordinals' => ['one' => 'first', 'two' => 'second', 'hundred' => 'hundredth'],
    'currencies' => [
        'USD' => ['main_singular' => 'dollar', 'main_plural' => 'dollars', 'sub_singular' => 'cent', 'sub_plural' => 'cents'],
    ],
    'units' => [
        'kg' => ['singular' => 'kilogram', 'plural' => 'kilograms'],
    ],
    'weekdays' => [
        'monday' => 'monday',
        'tuesday' => 'tuesday',
        'wednesday' => 'wednesday',
        'thursday' => 'thursday',
        'friday' => 'friday',
        'saturday' => 'saturday',
        'sunday' => 'sunday',
    ],
];
