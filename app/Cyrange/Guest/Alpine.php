<?php

namespace App\Cyrange\Guest;

/**
 * Defines how to execute configuration tasks on a Alpine guest.
 * Inspired by Vagrant:
 * https://github.com/hashicorp/vagrant/blob/main/plugins/guests/alpine/cap/halt.rb
 *
 * @author tibo
 */
class Alpine extends Ubuntu
{
    /**
     * This is currently the default guest...
     * @return bool
     */
    public function detect(): bool
    {
        return $this->test('cat /etc/alpine-release');
    }

    public function shutdown() : void
    {
        $this->exec("sudo poweroff");
    }

    public function configureNettools($interfaces): void
    {
        parent::configureNettools($interfaces);
        $this->configureDNS($interfaces);
    }

    /**
     * Add dns servers to /etc/resolv.conf
     * @param array<\App\Cyrange\InterfaceBlueprint> $interfaces
     */
    public function configureDNS(array $interfaces)
    {
        $s = "";
        foreach ($interfaces as $interface) {
            /** @var \App\Cyrange\InterfaceBlueprint $interface */
            if (!$interface->isDHCP() &&
                    $interface->dns_nameservers !== null) {
                $s .= "nameserver " . $interface->dns_nameservers . "\n";
            }
        }
        $this->exec("echo '$s' | sudo tee /etc/resolv.conf");
    }
}
