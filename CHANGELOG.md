# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added
- Add support for PHP v7.4 & v8.0 .
- Type declarations have been added to all method parameters and return types
  where possible.
- Constant `Consistent::HASH_AVAILABLE` to make it easier to extend the class to
  add alternate hash methods.
### Changed
- **BC break**: Reduce visibility of internal methods and properties. These
  members are not part of the public API. No impact to standard use of this
  package. If an implementation has a use case which needs to override these
  members, please submit a pull request explaining the change.
### Removed
- **BC break**: Removed support for PHP versions <= v7.3 as they are no longer
  [actively supported](https://php.net/supported-versions.php) by the PHP project.

## [1.0.0] - 2015-02-22
- Initial release.
