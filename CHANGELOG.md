# Changelog

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

See git history for earlier releases (German strategy branch, initial multilingual support).
