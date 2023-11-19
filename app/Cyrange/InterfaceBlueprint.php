<?php

namespace App\Cyrange;

/**
 * The blueprint for the network interface of a future VM
 *
 * @author tibo
 */
class InterfaceBlueprint
{

    const BRIDGED = "bridged";
    const VIRTUAL = "virtual";

    /**
     *
     * @var string
     */
    public $mode = self::BRIDGED;

    /**
     *
     * @var string
     */
    public $network = "INTERNET";

    /**
     *
     * @var ?string
     */
    public $address;

    /**
     *
     * @var string
     */
    public $mask;

    /**
     *
     * @var string
     */
    public $gateway;

    /**
     *
     * @var string
     */
    public $dns_nameservers;

    /**
     *
     * @var string
     */
    public $dns_search;

    public function isDHCP() : bool
    {
        return $this->address === null;
    }

    public function setDHCP() : void
    {
        $this->address = null;
    }

    public function setMode(string $mode) : void
    {
        if ($mode != self::BRIDGED && $mode != self::VIRTUAL) {
            throw new \Exception("Invalide mode $mode");
        }

        $this->mode = $mode;
    }
}
