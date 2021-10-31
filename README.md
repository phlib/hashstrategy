# phlib/hashstrategy
PHP hash strategy library including random, sorted, consistent

[![Code Checks](https://img.shields.io/github/workflow/status/phlib/hashstrategy/CodeChecks?logo=github)](https://github.com/phlib/hashstrategy/actions/workflows/code-checks.yml)
[![Codecov](https://img.shields.io/codecov/c/github/phlib/hashstrategy.svg?logo=codecov)](https://codecov.io/gh/phlib/hashstrategy)
[![Latest Stable Version](https://img.shields.io/packagist/v/phlib/hashstrategy.svg?logo=packagist)](https://packagist.org/packages/phlib/hashstrategy)
[![Total Downloads](https://img.shields.io/packagist/dt/phlib/hashstrategy.svg?logo=packagist)](https://packagist.org/packages/phlib/hashstrategy)
![Licence](https://img.shields.io/github/license/phlib/hashstrategy.svg)

## Usage

### Strategies

Values are added to an index with a specific weighting.
They are fetched according to the strategy and the hashing `$key` passed to `get()`.

- Consistent
  - The same value(s) will always be returned for the given `$key`.
  - Weighting is used to increase the likelihood of a value being returned.
- Ordered
  - Values are returned in order of decreasing weight.
  - Values with the same weight are returned in the order added.
- Rand
  - Values are picked randomly.
  - Weighting is used to increase the likelihood of a value being returned.

```php
$pool = new \Phlib\HashStrategy\Consistent();
$pool->add('one');
$pool->add('two');
$pool->add('three');
var_dump($pool->get('hello', 2));
```

### ConfigPool

Combines available hash strategies with a set of configs to provide a direct way
to choose a config to use, for example choosing between a set of replicas.

```php
$config = [
     'server1' => ['hostname' => 'localhost', 'port' => 11211],
     'server2' => ['hostname' => 'localhost', 'port' => 11212],
     'server3' => ['hostname' => 'localhost', 'port' => 11213],
];
$hashStrategy = new \Phlib\HashStrategy\Consistent();
$pool = new \Phlib\HashStrategy\ConfigPool($config, $hashStrategy);
var_dump($pool->getManyConfigs('some key', 2));
```

## License

This package is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
