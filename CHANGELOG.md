# Changelog

## Unreleased

### Added

- Field `priority` in job creation and job responses, taking a value of either `admin` (the highest priority), `user`
  (the default priority), or `script` (the lowest priority).
- New order `priority` to use the priority as first sorting criterion, and the creation time as second one. 

## 1.0.0 - 2020-03-26

- Initial release of the export queue server project.
