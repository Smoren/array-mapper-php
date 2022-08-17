<?php

namespace Smoren\ArrayMapper\Tests\Unit;

use Smoren\ArrayMapper\ArrayMapper;
use Smoren\ArrayMapper\ArrayMapperException;

class ArrayMapperTest extends \Codeception\Test\Unit
{
    public function testArrays()
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
        $this->assertEquals(['Russia', 'Belarus'], array_keys($result));
        $this->assertEquals(['Moscow', 'Tomsk'], array_keys($result['Russia']));
        $this->assertEquals(['Minsk'], array_keys($result['Belarus']));
        $this->assertEquals([1, 2], $result['Russia']['Moscow']);
        $this->assertEquals([3], $result['Russia']['Tomsk']);
        $this->assertEquals([4], $result['Belarus']['Minsk']);

        $result = ArrayMapper::map($source, ['country', 'city'], true, true);
        $this->assertEquals(['Russia', 'Belarus'], array_keys($result));
        $this->assertEquals(['Moscow', 'Tomsk'], array_keys($result['Russia']));
        $this->assertEquals(['Minsk'], array_keys($result['Belarus']));
        $this->assertEquals(1, $result['Russia']['Moscow'][0]['id']);
        $this->assertEquals(2, $result['Russia']['Moscow'][1]['id']);
        $this->assertEquals(3, $result['Russia']['Tomsk'][0]['id']);
        $this->assertEquals(4, $result['Belarus']['Minsk'][0]['id']);

        $result = ArrayMapper::map($source, ['country', 'city'], false, true);
        $this->assertEquals(['Russia', 'Belarus'], array_keys($result));
        $this->assertEquals(['Moscow', 'Tomsk'], array_keys($result['Russia']));
        $this->assertEquals(['Minsk'], array_keys($result['Belarus']));
        $this->assertEquals(2, $result['Russia']['Moscow']['id']);
        $this->assertEquals(3, $result['Russia']['Tomsk']['id']);
        $this->assertEquals(4, $result['Belarus']['Minsk']['id']);

        try {
            ArrayMapper::map($source, ['country', 'city'], false, false);
            $this->assertTrue(false);
        } catch(ArrayMapperException $e) {
            $this->assertEquals(ArrayMapperException::STATUS_FIELD_NOT_EXIST, $e->getCode());
        }
    }

    public function testObjects()
    {
        $source = [
            (object)[
                'id' => 1,
                'country' => 'Russia',
                'city' => 'Moscow',
            ],
            (object)[
                'id' => 2,
                'country' => 'Russia',
                'city' => 'Moscow',
            ],
            (object)[
                'id' => 3,
                'country' => 'Russia',
                'city' => 'Tomsk',
            ],
            (object)[
                'id' => 4,
                'country' => 'Belarus',
                'city' => 'Minsk',
            ],
            (object)[
                'id' => 5,
                'country' => 'Belarus',
            ],
        ];

        $result = ArrayMapper::map($source, ['country', 'city'], true, true, function($item) {
            return $item->id;
        });
        $this->assertEquals(['Russia', 'Belarus'], array_keys($result));
        $this->assertEquals(['Moscow', 'Tomsk'], array_keys($result['Russia']));
        $this->assertEquals(['Minsk'], array_keys($result['Belarus']));
        $this->assertEquals([1, 2], $result['Russia']['Moscow']);
        $this->assertEquals([3], $result['Russia']['Tomsk']);
        $this->assertEquals([4], $result['Belarus']['Minsk']);

        $result = ArrayMapper::map($source, ['country', 'city'], true, true);
        $this->assertEquals(['Russia', 'Belarus'], array_keys($result));
        $this->assertEquals(['Moscow', 'Tomsk'], array_keys($result['Russia']));
        $this->assertEquals(['Minsk'], array_keys($result['Belarus']));
        $this->assertEquals(1, $result['Russia']['Moscow'][0]->id);
        $this->assertEquals(2, $result['Russia']['Moscow'][1]->id);
        $this->assertEquals(3, $result['Russia']['Tomsk'][0]->id);
        $this->assertEquals(4, $result['Belarus']['Minsk'][0]->id);

        $result = ArrayMapper::map($source, ['country', 'city'], false, true);
        $this->assertEquals(['Russia', 'Belarus'], array_keys($result));
        $this->assertEquals(['Moscow', 'Tomsk'], array_keys($result['Russia']));
        $this->assertEquals(['Minsk'], array_keys($result['Belarus']));
        $this->assertEquals(2, $result['Russia']['Moscow']->id);
        $this->assertEquals(3, $result['Russia']['Tomsk']->id);
        $this->assertEquals(4, $result['Belarus']['Minsk']->id);

        try {
            ArrayMapper::map($source, ['country', 'city'], false, false);
            $this->assertTrue(false);
        } catch(ArrayMapperException $e) {
            $this->assertEquals(ArrayMapperException::STATUS_FIELD_NOT_EXIST, $e->getCode());
        }
    }

    public function testCallableFields()
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

        $this->assertEquals(['Russia', 'Belarus'], array_keys($result));
        $this->assertEquals(['Moscow-1' => 1, 'Moscow-2' => 2, 'Tomsk-3' => 3], $result['Russia']);
        $this->assertEquals(['Minsk-4' => 4], $result['Belarus']);
    }
}
