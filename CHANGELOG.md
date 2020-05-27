# Changelog

## Unreleased

### Added

- Field `priority` in job creation and job responses, taking a value of either `admin` (the highest priority), `user`
  (the default priority), or `script` (the lowest priority).
- New order `priority` to use the priority as first sorting criterion, and the creation time as second one.

### Changed

- Dependency `factorio-item-browser/export-queue-client` to version 1.2.
- Dependency `dasprid/container-interop-doctrine` to `roave/psr-container-doctrine`. 

## 1.0.0 - 2020-03-26

- Initial release of the export queue server project.
