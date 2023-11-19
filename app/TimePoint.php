<?php
namespace App;

/**
 * Represents a point in time (t, value). Used for ChartJS datasets.
 *
 * @author tibo
 */
class TimePoint
{
    public $t = 0;
    public $y = 0;

    public function __construct($t, $y)
    {
        $this->t = $t;
        $this->y = $y;
    }
}
