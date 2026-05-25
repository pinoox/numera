# Changelog

## 2.1.0 — 2026-05-25

### Added

- **Input formats**: European (`1.234.567,89`), Swiss (`1'234'567.89`), Persian/Arabic digits, underscore separators — `Numera::detectFormat()`
- **Fractions**: `toFraction()` / `n2f()` with `fractions` keys in `en` and `fa`
- **Years**: `toYear()` / `n2y()` — English pair reading (e.g. 2024 → twenty twenty-four)
- **Phone numbers**: `toPhone()` / `n2p()` — digit-by-digit, locale-aware
- **Roman numerals**: `toRoman()` / `n2r()`, static `Numera::fromRoman()` (1–3999)
- **Utility reading**: `toIp()` / `n2ip()`, `toVersion()` for IPv4 and semver strings
- **Laravel package**: `pinoox/numera-laravel` in `packages/laravel/` (ServiceProvider, Facade, config)
- **CI**: GitHub Actions (PHP 8.0–8.3, PHPUnit coverage, Laravel package tests)
- New support classes under `src/Support/` and `src/Languages/EnglishStrategy.php`

### Documentation

- Expanded README (API reference, quick start, monorepo layout)
- `packages/laravel/README.md` for Laravel integration

## 2.0.0 — 2026-05-25

### Added

- **184 ISO 639-1** language packs under `src/lang/` with v1.2+ keys (`negative`, `point`, `ordinals`, `currencies`, `units`, `weekdays`)
- **Regional variants** (BCP 47): `en-US`, `en-GB`, `fa-IR`, `de-DE`, … — inherit from base locale via `LocaleRegistry`
- **Weekdays**: `toWeekday()`, `getWeekdays()`, `d2w()` with `meta.week_starts_on` (e.g. Persian week from Saturday)
- **Cross-locale translation**: `translateTo()` / `t2t()` — words → number/weekday key → target locale words
- **Number strategies**: `german` (de), `french` (fr), `multiplier_tens` (id, vi, ms, …) via `meta.strategy`
- Negative numbers, decimals, ordinals, currency (`toCurrency`), units (`withUnit`)
- `LocaleRegistry`, `StrategyResolver`, coverage and feature tests (1000+ assertions)

### Changed

- All translatable strings live in `src/lang/{locale}.php`; numeric structure remains in `src/data/numbers.php`
- German compounding uses `GermanNumberStrategy` (inverted tens, `tausend` concatenation)

### Removed

- Legacy demo `index.php` and one-off maintenance scripts under `tools/`

## 1.x

See git history for earlier releases.
