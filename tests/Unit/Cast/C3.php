<?php
namespace Tests\Unit\Cast;

/**
 * Description of C3
 *
 * @author tibo
 */
class C3 extends C1
{
    public function __construct(C1 $object)
    {
        // Initializing class properties
        foreach ($object as $property => $value) {
            $this->$property = $value;
        }
    }
    
    public function getA()
    {
        return $this->a;
    }
}
