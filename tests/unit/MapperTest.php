<?php

namespace Smoren\ArrayMapper\Tests\Unit;


use Smoren\ArrayMapper\ArrayMapper;

class MapperTest extends \Codeception\Test\Unit
{
    public function testDefaultDelimiter()
    {
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

        $result = ArrayMapper::map($source, ['country', 'city'], true, true, function($item) {
            return $item['id'];
        });
        $a = 1;
        $c = 1;
    }
}
