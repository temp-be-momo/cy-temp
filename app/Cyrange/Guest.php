<?php

namespace App\Cyrange;

use phpseclib3\Net\SFTP;

/**
 * Description of Guest
 *
 * @author tibo
 */
abstract class Guest
{

    /**
     *
     * @var \phpseclib3\Net\SFTP
     */
    protected $ssh;

    public function __construct(SFTP $ssh)
    {
        $this->ssh = $ssh;
    }

    /**
     * Check if the VM is running this kind of Guest.
     */
    abstract public function detect() : bool;

    /**
     * Execute a command on the guest and return the result
     */
    abstract public function exec(string $command) : string;

    /**
     * Set the hostname of the guest
     */
    abstract public function setHostname(string $hostname) : void;

    /**
     * Change the password of the user
     */
    abstract public function setPassword(string $password) : void;

    /**
     * Shutdown the VM
     */
    abstract public function shutdown() : void;

    /**
     * @param array<InterfaceBlueprint> $interfaces
     */
    abstract public function configureNetworkInterfaces(array $interfaces) : void;
}
