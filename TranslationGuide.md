**Translation Guide**
=====================
### Description
The Numera translation guide is a comprehensive resource for creating language files for the Numera component. **Every translatable string belongs in `src/lang/{locale}.php`** (for example `src/lang/en.php`, `src/lang/fa.php`). The `src/data/` directory only holds non-translatable data (`numbers.php`, `iso639-1.php`). See `src/lang/_schema.php` and `src/lang/en.php` for a full v1.2 example including ordinals, currencies, and units.

### Numbers

| Key           | English Translation (Camel Case) | Required |
|---------------|----------------------------------|----------|
| zero          | Zero                             | *        |
| one           | One                              | *        |
| two           | Two                              | *        |
| three         | Three                            | *        |
| four          | Four                             | *        |
| five          | Five                             | *        |
| six           | Six                              | *        |
| seven         | Seven                            | *        |
| eight         | Eight                            | *        |
| nine          | Nine                             | *        |
| ten           | Ten                              | *        |
| eleven        | Eleven                           | *        |
| twelve        | Twelve                           | *        |
| thirteen      | Thirteen                         | *        |
| fourteen      | Fourteen                         | *        |
| fifteen       | Fifteen                          | *        |
| sixteen       | Sixteen                          | *        |
| seventeen     | Seventeen                        | *        |
| eighteen      | Eighteen                         | *        |
| nineteen      | Nineteen                         | *        |
| twenty        | Twenty                           | *        |
| thirty        | Thirty                           | *        |
| forty         | Forty                            | *        |
| fifty         | Fifty                            | *        |
| sixty         | Sixty                            | *        |
| seventy       | Seventy                          | *        |
| eighty        | Eighty                           | *        |
| ninety        | Ninety                           | *        |
| one_hundred   | Own Hundred                      | -        |
| two_hundred   | Two Hundred                      | -        |
| three_hundred | Three Hundred                    | -        |
| four_hundred  | Four Hundred                     | -        |
| five_hundred  | Five Hundred                     | -        |
| sex_hundred   | Sex Hundred                      | -        |
| seven_hundred | Seven Hundred                    | -        |
| eight_hundred | Eight Hundred                      | -        |
| nine_hundred  | Nine Hundred                    | -        |

### Units

| Key | English Translation (Camel Case) | Required |
| --- | --- | --- |
| hundred | Hundred | * |
| thousand | Thousand |* |
| million | Million | * |
| billion | Billion |* |
| trillion | Trillion |* |
| quadrillion | Quadrillion |* |
| quintillion | Quintillion |* |

### Separators

| Key | English Translation (Camel Case) | Required |
| --- |----------------------------------|----------|
| between | '&emsp;'                         | -        |
| between.part | ' , '                            | -        |
| between.thousand | '&emsp;'                         | -        |
| between.ten | ' - '                            | -        |
| between.hundred | '&emsp;'                         | -        |
| between.hundred.prefix | '&emsp;'                         | -        |
| between.decimal | ' ' (digit separator in decimals) | -        |

### Extension keys (v1.2+, all in `src/lang/{locale}.php`)

| Key | Description |
| --- | --- |
| negative | Word for minus sign (e.g. `negative`, `منفی`) |
| point | Decimal point word (e.g. `point`, `ممیز`) |
| and | Conjunction for currency phrases (e.g. `and`, `و`) |
| ordinal_suffix | Default ordinal suffix (Persian: `م`) |
| ordinal_suffixes | English-style [th, st, nd, rd, th] |
| ordinal_exceptions | Map of int → ordinal word for 1–20 (and 11–13) |
| ordinals | Map of cardinal key → ordinal word (`one` → `first`) |
| currencies | Nested map per code (`USD`, `EUR`, …) with `main_singular`, `main_plural`, optional `sub_*` |
| units | Map per unit (`kg`, `hour`, …) with `singular` / `plural` |

**Sample PHP Code**
-------------------

Here is an example of how the translation guide can be used in PHP:
```php
<?php
return [
    'zero' => 'Zero',
    'one' => 'One',
    'two' => 'Two',
    // ...
    'hundred' => 'Hundred',
    'thousand' => 'Thousand',
    'million' => 'Million',
    // ...
    'between' => 'Between',
    'between.part' => 'BetweenPart',
];
```

### Optional keys (v2.1+)

For extended reading modes, add these blocks to fully extended locales (see `src/lang/en.php`):

| Block | Used by |
|-------|---------|
| `fractions` | `toFraction()` / `n2f()` — `half`, `quarter`, `third`, `one`, `and`, `article`, `whole_*` |
| `plus` | `toPhone()` — spoken “plus” before country code |
| `dot` | `toIp()` — separator between octets |
| `dash` | `toVersion()` — prerelease separator (e.g. `-beta`) |
| `weekdays` | `toWeekday()`, `getWeekdays()` |
| `currencies`, `units`, `ordinals` | `toCurrency()`, `withUnit()`, `toOrdinal()` |

English year pairs (`toYear`) use built-in logic in `src/Languages/EnglishStrategy.php`; other locales fall back to cardinal `n2w()`.

