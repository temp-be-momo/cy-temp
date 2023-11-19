<?php

namespace App\Cyrange;

use Cylab\Vbox\VM;

/**
 * The blueprint for a future VM...
 *
 * @author tibo
 */
class Blueprint
{

    /**
     *
     * @var string
     */
    private $image;

    /**
     *
     * @var string
     */
    private $name = "noname";

    /**
     *
     * @var string
     */
    private $group_name = "/undefined";

    /**
     *
     * @var string
     */
    private $hostname = "vm";

    /**
     *
     * @var string
     */
    private $password = "undefined";

    /**
     *
     * @var int
     */
    private $memory = 1024;

    /**
     *
     * @var int
     */
    private $cpu_count = 2;

    /**
     *
     * @var int
     */
    private $cpu_cap = 50;

    /**
     *
     * @var bool
     */
    private $need_rdp = true;

    /**
     *
     * @var bool
     */
    private $need_guest_config = true;

    /**
     *
     * @var string[]
     */
    private $provision = [];

    /**
     *
     * @var InterfaceBlueprint[]
     */
    private $interfaces = [];

    /**
     *
     * @var string
     */
    private $playbook = null;

    /**
     *
     * @var VM
     */
    private $vm = null;

    /**
     *
     * @return InterfaceBlueprint[]
     */
    public function interfaces()
    {
        return $this->interfaces;
    }

    public function getImage() : string
    {
        return $this->image;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getHostname() : string
    {
        return $this->hostname;
    }

    public function getMemory() : int
    {
        return $this->memory;
    }

    public function getCpuCount() : int
    {
        return $this->cpu_count;
    }

    public function getCpuCap() : int
    {
        return $this->cpu_cap;
    }

    public function getNeedRdp() : bool
    {
        return $this->need_rdp;
    }

    /**
     *
     * @return string[]
     */
    public function getProvision() : array
    {
        return $this->provision;
    }

    /**
     *
     * @return InterfaceBlueprint[]
     */
    public function getInterfaces() : array
    {
        return $this->interfaces;
    }

    public function setImage(string $image) : Blueprint
    {
        $this->image = $image;
        return $this;
    }

    public function setName(string $name) : Blueprint
    {
        $this->name = $name;
        return $this;
    }

    public function setHostname(string $hostname) : Blueprint
    {
        $this->hostname = $hostname;
        return $this;
    }

    public function setMemory(int $memory) : Blueprint
    {
        $this->memory = $memory;
        return $this;
    }

    public function setCpuCount(int $cpu_count) : Blueprint
    {
        $this->cpu_count = $cpu_count;
        return $this;
    }

    public function setCpuCap(int $cpu_cap) : Blueprint
    {
        $this->cpu_cap = $cpu_cap;
        return $this;
    }

    public function setNeedRdp(bool $need_rdp) : Blueprint
    {
        $this->need_rdp = $need_rdp;
        return $this;
    }

    public function setNeedGuestConfig(bool $need_guest_config) : Blueprint
    {
        $this->need_guest_config = $need_guest_config;
        return $this;
    }

    public function needGuestConfig() : bool
    {
        return $this->need_guest_config;
    }

    /**
     *
     * @param string[] $provision
     * @return Blueprint
     */
    public function setProvision(array $provision) : Blueprint
    {
        $this->provision = $provision;
        return $this;
    }

    /**
     *
     * @param InterfaceBlueprint[] $interfaces
     * @return $this
     */
    public function setInterfaces(array $interfaces) : Blueprint
    {
        $this->interfaces = $interfaces;
        return $this;
    }

    public function addInterface(InterfaceBlueprint $interface) : Blueprint
    {
        $this->interfaces[] = $interface;
        return $this;
    }

    public function getGroupName() : string
    {
        return $this->group_name;
    }

    public function setGroupName(string $group_name) : Blueprint
    {
        $this->group_name = $group_name;
        return $this;
    }

    public function hasPlaybook() : bool
    {
        return $this->playbook !== null;
    }

    public function getPlaybook() : string
    {
        return $this->playbook;
    }

    public function setPlaybook(string $playbook) : Blueprint
    {
        $this->playbook = $playbook;
        return $this;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function setPassword(string $password) : Blueprint
    {
        $this->password = $password;
        return $this;
    }

    /**
     *
     * @return \Cylab\Vbox\VM;
     */
    public function getVm() : ?VM
    {
        return $this->vm;
    }

    public function setVm(VM $vm) : Blueprint
    {
        $this->vm = $vm;
        return $this;
    }
}
