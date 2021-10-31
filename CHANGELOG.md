# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added
- Add support for PHP v7.1 (temporary measure to move away from PHP v5).
- Type declarations have been added to all method parameters and return types
  where possible.
- Constant `Consistent::HASH_AVAILABLE` to make it easier to extend the class to
  add alternate hash methods.
### Removed
- **BC break**: Removed support for PHP versions < v7.1 as they are no longer
  [actively supported](https://php.net/supported-versions.php) by the PHP project.

## [1.0.0] - 2015-02-22
- Initial release.
