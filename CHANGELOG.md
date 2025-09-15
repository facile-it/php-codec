# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.0.4] - 2025-09-15
### Added
- `DateTimeFromStringDecoder` now supports strict mode configuration to enforce strict date parsing (default: true). In strict mode, invalid dates like "2025-04-31" will be rejected instead of being automatically adjusted. (#141)

### Removed
- Support for PHP < 7.4
- `Codec` and `Encoder` interfaces. Removed `Codecs` entrypoint.

## [0.0.3] - 2022-01-16
### Added
- `SimplePathReporter` error reporter.
### Changed
- `Facile\PhpCodec\PathReporter` moved to `Facile\PhpCodec\Reporters\PathReporter`.

## [0.0.2] - 2021-08-13
### Added
- `trasformValidationSuccess` function for decoders. Structurally equivalent to a map. (#24)
- Decoders to replace codecs. (#33)
### Changed
- `Validation::sequence` moved to `ListOfValidation::sequence`. (#33)
### Deprecated
- The usage of codecs is deprecated in favour of decoders. (#24)
### Fixed
- Used Psalm specific annotations to avoid confusing IDEs without Psalm support. (#26)
- Every class in the namespace `Facile\PhpCodec\Internal` is marked as internal, and it should not be used outside. (#33)
### Removed
- `Facile\PhpCodec\Internal\Type`. (#33)
- `Facile\PhpCodec\Refiner`. (#33)

## [0.0.1] - 2021-04-30 
### Added
- Defined project's basic architecture
- Codecs for primitive php types
- Codecs for arrays
- Codecs for building class instances from array versions of them
- The union codec
- The pipe function and the composition codec

[Unreleased]: https://github.com/facile-it/php-codec/compare/0.0.4...HEAD
[0.0.4]: https://github.com/facile-it/php-codec/compare/0.0.3...0.0.4
[0.0.3]: https://github.com/facile-it/php-codec/compare/0.0.2...0.0.3
[0.0.2]: https://github.com/facile-it/php-codec/compare/0.0.1...0.0.2
[0.0.1]: https://github.com/facile-it/php-codec/releases/tag/0.0.1
