<?php

namespace App\Cyrange\Guest;

use App\Cyrange\Guest;

use Symfony\Component\Yaml\Yaml;

/**
 * Defines how to execute configuration tasks on a Ubuntu guest.
 * Inspired by Vagrant:
 * https://github.com/hashicorp/vagrant/blob/master/plugins/guests/linux/cap/network_interfaces.rb
 * https://github.com/hashicorp/vagrant/blob/master/plugins/guests/debian/cap/configure_networks.rb
 *
 * @author tibo
 */
class Ubuntu extends Guest
{
    /**
     * This is currently the default guest...
     * @return bool
     */
    public function detect(): bool
    {
        return true;
    }

    public function setHostname(string $hostname) : void
    {
        $this->exec("echo '$hostname\n' | sudo tee /etc/hostname");
        //$this->exec("echo '127.0.0.1  localhost\n127.0.1.1    $hostname' | sudo tee /etc/hosts");
        $this->exec("sed '/^127\\.0\\.1\\.1/ s/$/ $hostname/' filename");
    }

    public function setPassword(string $password) : void
    {
        $this->exec("echo 'vagrant:$password' | sudo chpasswd");
    }

    /**
     * Get an array of interfaces, from a list extracted from the guest using
     * sudo ip -o -0 addr | grep -v LOOPBACK | awk '{print $2}' | sed 's/://'
     * @param String $interfaces
     * @return array<string> Description
     */
    public function parseInterfaces(string $interfaces) : array
    {
        $allowed_prefixes = ["en", "eth"];
        $interfaces = explode("\n", $interfaces);
        $allowed_interfaces = [];
        foreach ($interfaces as $interface) {
            $interface = trim($interface);
            if ($this->startsWith($interface, $allowed_prefixes)) {
                $allowed_interfaces[] = $interface;
            }
        }

        natsort($allowed_interfaces);
        return $allowed_interfaces;
    }

    /**
     *
     * @param string $string
     * @param array<string> $needles
     * @return boolean
     */
    public function startsWith(string $string, array $needles) : bool
    {
        foreach ($needles as $needle) {
            if (substr($string, 0, strlen($needle)) === $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Execute a command in the Guest.
     * @param string $command
     * @return string
     */
    public function exec(string $command) : string
    {
        return $this->ssh->exec($command);
    }

    /**
     * Test if the command succeeds.
     * @param string $command
     * @return bool
     */
    public function test(string $command) : bool
    {
        $this->ssh->exec($command);
        return $this->ssh->getExitStatus() == 0;
    }

    public function hasNetplan() : bool
    {
        return $this->test("command -v netplan");
    }

    public function shutdown() : void
    {
        $this->exec("sudo shutdown now");
    }

    /**
     *
     * @param array<\App\Cyrange\InterfaceBlueprint> $interfaces
     * @return void
     */
    public function configureNetworkInterfaces(array $interfaces) : void
    {
        if ($this->hasNetplan()) {
            $this->configureNetplan($interfaces);
        } else {
            $this->configureNettools($interfaces);
        }
    }

    /**
     *
     * @param array<\App\Cyrange\InterfaceBlueprint> $interfaces
     * @return void
     */
    public function configureNetplan(array $interfaces) : void
    {
        $interfaces_list = $this->getInterfacesList();
        $np_string = $this->configureNetplanString($interfaces, $interfaces_list);
        $this->exec("echo '$np_string' | sudo tee /etc/netplan/99-cyrange.yaml");
    }

    /**
     *
     * @param array<\App\Cyrange\InterfaceBlueprint> $interfaces
     * @param array<string> $interfaces_list the list of enabled interfaces on the guest VM
     * @return string
     */
    public function configureNetplanString(array $interfaces, array $interfaces_list) : string
    {
        $nets = [];
        foreach ($interfaces as $i => $interface) {
            /** @var \App\Cyrange\InterfaceBlueprint $interface */
            $interface_name = $interfaces_list[$i];

            if ($interface->isDHCP()) {
                $nets[$interface_name] = [
                    "dhcp4" => true,
                    "dhcp-identifier" => "mac"];
                continue;
            }

            $nets[$interface_name] = [
                "dhcp4" => false,
                "dhcp6" => false,
                "addresses" => [
                    $interface->address . "/" . $this->mask2cidr($interface->mask)]];

            if ($interface->gateway !== null) {
                $nets[$interface_name]["gateway4"] = $interface->gateway;
            }

            if ($interface->dns_nameservers !== null) {
                $nets[$interface_name]["nameservers"]["addresses"] = [$interface->dns_nameservers];
            }

            if ($interface->dns_search !== null) {
                $nets[$interface_name]["nameservers"]["search "] = [$interface->dns_search];
            }
        }

        $netplan = ["network" => ["version" => 2, "ethernets" => $nets]];
        return Yaml::dump($netplan, 4, 4);
    }

    public function mask2cidr(string $mask) : int
    {
        $long = ip2long($mask);
        $base = ip2long('255.255.255.255');
        return (int) (32-log(($long ^ $base)+1, 2));
    }

    /**
     *
     * @param array<\App\Cyrange\InterfaceBlueprint> $interfaces
     */
    public function configureNettools(array $interfaces) : void
    {
        $interfaces_list = $this->getInterfacesList();
        $interfaces_string = $this->configureNettoolsString($interfaces, $interfaces_list);
        $this->exec("echo '$interfaces_string' | sudo tee /etc/network/interfaces");
    }

    /**
     * Get the list of interfaces enabled on the guest VM.
     * @return array<string>
     */
    public function getInterfacesList() : array
    {
        return $this->parseInterfaces(
            $this->ssh->exec(
                "sudo ip -o -0 addr | grep -v LOOPBACK | awk '{print $2}' | sed 's/://'"
            )
        );
    }

    /**
     *
     * @param array<\App\Cyrange\InterfaceBlueprint> $interfaces
     * @param array<string> $list the list of enable interfaces on the guest VM
     * @return string
     */
    public function configureNettoolsString(array $interfaces, array $list) : string
    {
        $string = "";
        foreach ($interfaces as $i => $interface) {
            $interface_name = $list[$i];

            if ($interface->isDHCP()) {
                $string .= "auto $interface_name\n"
                    . "iface $interface_name inet dhcp\n\n";
            } else {
                $string .= "auto " . $interface_name . "\n"
                . "iface " . $interface_name . " inet static\n"
                . "address " . $interface->address . "\n"
                . "netmask " . $interface->mask . "\n";

                if ($interface->gateway !== null) {
                    $string .= "gateway " . $interface->gateway . "\n";
                }

                if ($interface->dns_nameservers !== null) {
                    $string .= "dns-nameservers " . $interface->dns_nameservers . "\n";
                }

                if ($interface->dns_search !== null) {
                    $string .= "dns-search " . $interface->dns_search . "\n";
                }

                $string .= "\n";
            }
        }

        return $string;
    }
}
