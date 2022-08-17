# array-mapper

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/smoren/array-mapper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Smoren/array-mapper-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Smoren/array-mapper-php/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/Smoren/array-mapper-php/badge.svg?branch=master)](https://coveralls.io/github/Smoren/array-mapper-php?branch=master)
![Build and test](https://github.com/Smoren/array-mapper-php/actions/workflows/test_master.yml/badge.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Helper for mapping arrays

### How to install to your project
```
composer require smoren/array-mapper
```

### Unit testing
```
composer install
./vendor/bin/codecept build
./vendor/bin/codecept run unit tests/unit
```

### Usage

```php
use Smoren\ArrayMapper\ArrayMapper;

$source = [
    [
        'id' => 1,
        'country' => 'Russia',
        'city' => 'Moscow',
    ],
    [
        'id' => 2,
        'country' => 'Russia',
        'city' => 'Moscow',
    ],
    [
        'id' => 3,
        'country' => 'Russia',
        'city' => 'Tomsk',
    ],
    [
        'id' => 4,
        'country' => 'Belarus',
        'city' => 'Minsk',
    ],
    [
        'id' => 5,
        'country' => 'Belarus',
    ],
];

$result = ArrayMapper::map($source, ['country', 'city'], true, true);

print_r($result);
/*
Array
(
    [Russia] => Array
        (
            [Moscow] => Array
                (
                    [0] => Array
                        (
                            [id] => 1
                            [country] => Russia
                            [city] => Moscow
                        )
                    [1] => Array
                        (
                            [id] => 2
                            [country] => Russia
                            [city] => Moscow
                        )
                )
            [Tomsk] => Array
                (
                    [0] => Array
                        (
                            [id] => 3
                            [country] => Russia
                            [city] => Tomsk
                        )
                )
        )
    [Belarus] => Array
        (
            [Minsk] => Array
                (
                    [0] => Array
                        (
                            [id] => 4
                            [country] => Belarus
                            [city] => Minsk
                        )
                )
        )
)
*/

$result = ArrayMapper::map($source, ['country', 'city'], true, true, function($item) {
    return $item['id'];
});

print_r($result);
/*
Array
(
    [Russia] => Array
        (
            [Moscow] => Array
                (
                    [0] => 1
                    [1] => 2
                )
            [Tomsk] => Array
                (
                    [0] => 3
                )
        )
    [Belarus] => Array
        (
            [Minsk] => Array
                (
                    [0] => 4
                )
        )
)
*/

$source = [
    [
        'id' => 1,
        'country' => 'Russia',
        'city' => 'Moscow',
    ],
    [
        'id' => 2,
        'country' => 'Russia',
        'city' => 'Moscow',
    ],
    [
        'id' => 3,
        'country' => 'Russia',
        'city' => 'Tomsk',
    ],
    [
        'id' => 4,
        'country' => 'Belarus',
        'city' => 'Minsk',
    ],
];

$mapFields = [
    'country',
    function($item) {
        return $item['city'].'-'.$item['id'];
    }
];

$result = ArrayMapper::map($source, $mapFields, false, true, function($item) {
    return $item['id'];
});

/*
Array
(
    [Russia] => Array
        (
            [Moscow-1] => 1
            [Moscow-2] => 2
            [Tomsk-3] => 3
        )
    [Belarus] => Array
        (
            [Minsk-4] => 4
        )
)
*/
```
