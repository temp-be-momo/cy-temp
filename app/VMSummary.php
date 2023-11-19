<?php

namespace App;

/**
 * Summarize VM information: total number of vCPU, memory etc.
 *
 * VM's, running VM's, vCPU's, vCPU's or running VM's etc...
 */
class VMSummary
{

    public $vm_count = 0;
    public $vcpu_count = 0;
    public $memory = 0;
    public $disk_size = 0;

    // VCPUs and memory used by running VMs
    public $vm_count_running = 0;
    public $vcpu_count_running = 0;
    public $memory_running = 0;
    
    private $power_per_core;             // W/core
    private $carbon_intensity;           // g/kWh
    private $month_duration = 30 * 24;   // h
    private $average_car_emission;       // g/km
    

    /**
     * You can only instantiate this class with the fromVMList static method.
     */
    private function __construct()
    {
        $this->power_per_core = config("co2.power_per_core");
        $this->carbon_intensity = config("co2.carbon_intensity");
        $this->average_car_emission = config("co2.average_car_emission");
    }
    
    public function powerPerCore() : float
    {
        return $this->power_per_core;
    }
    
    public function carbonIntensity() : float
    {
        return $this->carbon_intensity;
    }
    
    public function monthDuration() : int
    {
        return $this->month_duration;
    }
    
    public function averageCarEmission()
    {
        return $this->average_car_emission;
    }

    /**
     *
     * @param iterable $vms
     * @return VMSummary
     */
    public static function fromVMList(iterable $vms) : VMSummary
    {
        $status = new VMSummary();
        foreach ($vms as $vm) {
            if (!$vm->hasVBoxVM()) {
                continue;
            }

            /** @var \Cylab\Vbox\VM $vboxvm */
            $vboxvm = $vm->getVBoxVM();
            $status->vm_count++;
            $status->vcpu_count += $vboxvm->getCPUCount();
            $status->memory += $vboxvm->getMemorySize();
            $status->disk_size += $vm->totalStorageSize();

            
            if ($vboxvm->isRunning()) {
                $status->vm_count_running++;
                $status->vcpu_count_running += $vboxvm->getCPUCount();
                $status->memory_running += $vboxvm->getMemorySize();
            }
        }
        return $status;
    }
    
    /**
     * Compute the monthly CO2 emission of this list of VM, in g/month.
     * @return float
     */
    public function monthlyCO2() : float
    {
        return $this->monthlyEnergy() * $this->carbon_intensity;
    }
    
    /**
     * Compute the power consumption of this list of VM, in watts.
     * @return float
     */
    public function power() : float
    {
        return $this->power_per_core * $this->vcpu_count_running;
    }
    
    /**
     * Compute the monthly energy consumed by this list of VM, in kWh/month.
     * @return float
     */
    public function monthlyEnergy() : float
    {
        return $this->power() * $this->month_duration / 1000;
    }
    
    /**
     * Equivalent CO2 produced by a car, in km/month.
     * @return float
     */
    public function carEquivalent() : float
    {
        return $this->monthlyCO2() / $this->average_car_emission;
    }
}
