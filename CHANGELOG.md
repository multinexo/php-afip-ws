# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
- Some enums removed.

## [2.0.10] - 2025-01-18
### Added
- Some enums changed based on AFIP/ARCA.

## [2.0.9] - 2022-08-27
### Fixed
- empty exception on Wsfe.php:515 [#48](https://github.com/multinexo/php-afip-ws/issues/48)

## [2.0.8] - 2022-08-12
### Fixed
- ValidacionDeToken: No validaron las fechas del token GenTime, ExpTime, NowUTC [#45](https://github.com/multinexo/php-afip-ws/issues/45)

## [2.0.5] - 2021-07-31
### Changed
- CHANGELOG based on Keep aChangelog
- README with docker

### Fixed
- Error "puntoVenta must be in { }" [#35](https://github.com/multinexo/php-afip-ws/issues/35)

### Added
- AfipUnavailableServiceException

## [2.0.4] - 10-04-21

### Fixed
- Return type of callWSAA();

## [2.0.3] - 04-03-21

### Fixed
- AfipUnhandledException on WSFE.

### Changed
- Improvements: Syntax with `use` and Types on some functions.

## [2.0.2] - 30-07-20

### Added 
- AfipUnhandledException. Used for uncontrolled errors. You never send this data final user, it's only for debugging purposes. 

### Fixed
- Error when WSFE return an error without Observations

## [v2.0.0 (2019-08-08)](https://github.com/multinexo/php-afip-ws/releases/tag/2.0.0)

### Added
- Added class `CSRFile` to generation CSR file ([#695421a](https://github.com/multinexo/php-afip-ws/pull/10/commits/695421aa0a0efc72d3829b41bc54d8edc121f695))
- Added class `AfipConfig` to set configuration ([#391726d](https://github.com/multinexo/php-afip-ws/pull/12/commits/391726d066bb0fdd72d729174e49d190f266192a))
- Added urls of production and sandbox to config file ([#391726d](https://github.com/multinexo/php-afip-ws/pull/12/commits/391726d066bb0fdd72d729174e49d190f266192a))

### Changed
- New version to PHP 7.2
- Folder restructuring ([#3da9cbb](https://github.com/multinexo/php-afip-ws/pull/9/commits/3da9cbb31c80d91fac518d13fa3993edde3ee914))
- Renamed method `crearComprobante` for `createInvoice` ([#3da9cbb](https://github.com/multinexo/php-afip-ws/pull/9/commits/3da9cbb31c80d91fac518d13fa3993edde3ee914))
- Services refactoring ([#6dde466](https://github.com/multinexo/php-afip-ws/pull/14/commits/6dde466f4f7d48b7d7d89ec18b8348c8b98b73d5))
- Methods renamed ([#6dde466](https://github.com/multinexo/php-afip-ws/pull/14/commits/6dde466f4f7d48b7d7d89ec18b8348c8b98b73d5))

### Removed
- Removed `FacturaConItems.php` and `FacturaSinItems.php` ([#3da9cbb](https://github.com/multinexo/php-afip-ws/pull/9/commits/3da9cbb31c80d91fac518d13fa3993edde3ee914))
- Removed method `setearConfiguracion` in Auth ([#6dde466](https://github.com/multinexo/php-afip-ws/pull/14/commits/6dde466f4f7d48b7d7d89ec18b8348c8b98b73d5))
