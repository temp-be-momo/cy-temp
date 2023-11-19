<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * @group cast
 */
class ExampleTest extends TestCase
{
    public function testCast()
    {
        $c1 = new \Tests\Unit\Cast\C1();
        $c2 = $this->cast($c1, \Tests\Unit\Cast\C2::class);
        $this->assertEquals(123, $c2->getA());
    }

    public function testCastConstructor()
    {
        $c1 = new \Tests\Unit\Cast\C1();
        $c3 = new \Tests\Unit\Cast\C3($c1);
        $this->assertEquals(123, $c3->getA());
    }

    public function cast($object, string $class)
    {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($class),
            $class,
            strstr(strstr(serialize($object), '"'), ':')
        ));
    }
}
