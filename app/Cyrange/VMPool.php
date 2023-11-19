<?php

namespace App\Cyrange;

use Cylab\Vbox\VM;

/**
 * A pool of imported VM's, waiting to be configured.
 *
 * @author tibo
 */
class VMPool
{
    /**
     *
     * @var array<string, VM[]>
     */
    private $vms = [];

    public function add(string $image, VM $vm) : void
    {
        $this->vms[$image][] = $vm;
    }

    /**
     *
     * @param string $image
     * @param VM[] $vms
     * @return void
     */
    public function addMultiple(string $image, array $vms) : void
    {
        foreach ($vms as $vm) {
            $this->add($image, $vm);
        }
    }

    public function get(string $image) : VM
    {
        $vm = array_pop($this->vms[$image]);

        if ($vm === null) {
            throw new \Exception("No VM available for image $image");
        }

        return $vm;
    }
}
