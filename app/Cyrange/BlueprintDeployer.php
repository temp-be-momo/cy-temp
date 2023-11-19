<?php

namespace App\Cyrange;

use App\Cyrange\Guest\Ubuntu;
use App\Cyrange\Guest\Alpine;

use Cylab\Vbox\VBox;
use Cylab\Vbox\VM;
use Cylab\Vbox\NATRedirect;

use phpseclib3\Net\SFTP;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * A BlueprintDeployer is responsible for turning a blueprint into a running VM. This
 * includes deploying the image, configuring network, software etc.
 *
 * @author tibo
 */
class BlueprintDeployer
{

    const RDP_START_PORT = 15000;
    const RDP_END_PORT = 15999;

    const SSH_START_PORT = 22000;
    const SSH_END_PORT = 22999;

    const GUEST_IMPLEMENTATIONS=[Alpine::class, Ubuntu::class];

    /**
     *
     * @var VBox
     */
    private $vbox;

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(VBox $vbox, LoggerInterface $logger = null)
    {
        $this->vbox = $vbox;

        if ($logger == null) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $logger;
        }
    }

    /**
     *
     * @param \App\Cyrange\Blueprint $blueprint
     * @return VM
     */
    public function deploy(Blueprint $blueprint) : VM
    {
        $group = new BlueprintGroup();
        $group->blueprints[] = $blueprint;

        $groups = $this->deployAll([$group]);
        return $groups[0]->blueprints[0]->getVm();
    }

    /**
     *
     * @param array<BlueprintGroup> $blueprint_groups
     * @return BlueprintGroup[]
     */
    public function deployAll(array $blueprint_groups) : array
    {
        $bom = $this->computeBOM($blueprint_groups);
        $vm_pool = $this->importVMs($bom);
        return $this->configureVMs($blueprint_groups, $vm_pool);
    }

    /**
     * Compute a map [image => count].
     * @param array<BlueprintGroup> $blueprint_groups
     * @return array<string, int>
     */
    public function computeBOM(array $blueprint_groups): array
    {
        $bom = [];
        foreach ($blueprint_groups as $group) {
            foreach ($group->blueprints as $blueprint) {
                /** @var Blueprint $blueprint */
                if (! isset($bom[$blueprint->getImage()])) {
                    $bom[$blueprint->getImage()] = 0;
                }

                $bom[$blueprint->getImage()]++;
            }
        }

        return $bom;
    }

    /**
     * Import all required VM's. Returns a pool of VM's
     * @param array<string, int> $bom
     * @return VMPool
     */
    public function importVMs(array $bom) : VMPool
    {
        $vm_pool = new VMPool();
        foreach ($bom as $image => $count) {
            $this->logger->info("Import $count copies of $image ...");
            $vms = $this->vbox->importMultiple($image, $count);
            $vm_pool->addMultiple($image, $vms);
        }
        return $vm_pool;
    }

    /**
     *
     * @param BlueprintGroup[] $blueprint_groups
     * @param VMPool $vms
     * @return BlueprintGroup[]
     */
    public function configureVMs(array $blueprint_groups, VMPool $vms) : array
    {
        foreach ($blueprint_groups as $group) {
            foreach ($group->blueprints as $blueprint) {
                $image = $blueprint->getImage();
                $vm = $vms->get($image);
                $blueprint->setVm($this->configureVM($vm, $blueprint));
            }
        }
        return $blueprint_groups;
    }

        /**
     * Configure VM:
     * 1. VBox properties
     * 2. Hardware
     * 3. NAT+SSH+UP
     * 4. Guest OS: provision, hostname, password, network
     * 5. HALT
     * 6. Network
     * 7. UP
     *
     * @param \Cylab\Vbox\VM $vm
     * @param Blueprint $blueprint
     * @return VM
     */
    public function configureVM(VM $vm, Blueprint $blueprint) : VM
    {

        $this->logger->notice("Configure " . $blueprint->getName() . "...");

        $this->logger->notice("Set VBox properties...");
        $vm->setName($blueprint->getName());
        $vm->setGroups([$blueprint->getGroupName()]);

        $vm->getVRDEServer()->setEnabled($blueprint->getNeedRdp());
        if ($blueprint->getNeedRdp()) {
            $vm->getVRDEServer()->setBindAddress('0.0.0.0');
            $vm->getVRDEServer()->setPort($this->findOpenPort());
        }

        $this->logger->notice("Configure virtual hardware...");
        $vm->setCPUCount($blueprint->getCpuCount());
        $vm->setCPUCap($blueprint->getCpuCap());
        $vm->setMemorySize($blueprint->getMemory());

        $this->logger->notice("Enable required network interfaces...");
        for ($i = 0; $i < count($blueprint->interfaces()); $i++) {
            $vm->getNetworkAdapter($i)->setEnabled(true);
        }

        if ($blueprint->needGuestConfig()) {
            $this->logger->notice("Configure NAT and port forwarding...");

            $port = random_int(self::SSH_START_PORT, self::SSH_END_PORT);
            $this->logger->notice("Using port $port");

            $adapter = $vm->getNetworkAdapter(0);
            $adapter->nat()->addRedirect(
                new NATRedirect($port, 22, "TCP", getenv("VBOX_HOST"))
            );

            $this->logger->notice("Boot VM...");
            $vm->up();

            $this->logger->notice("SSH using port forwarding...");
            $this->logger->notice("vagrant@" . getenv("VBOX_HOST") . ":$port");

            $ssh = null;
            $connected = $this->retry(
                function () use (&$ssh, $port) {
                        $ssh = new SFTP(getenv("VBOX_HOST"), $port);
                    return $ssh->login('vagrant', 'vagrant');
                }
            );

            if (!$connected) {
                throw new \Exception("Could not connect to " . $blueprint->getName());
            }

            $ssh->setTimeout(600);

            $this->logger->notice("Configure guest VM...");
            /** @var Guest $guest */
            $guest = null;
            foreach (self::GUEST_IMPLEMENTATIONS as $implementation) {
                $this->logger->notice("Trying $implementation ...");
                $guest = new $implementation($ssh);
                if ($guest->detect()) {
                    break;
                }
            }


            // 4.1 Hostname
            // will modify /etc/hostname and /etc/hosts
            $guest->setHostname($blueprint->getHostname());


            $this->logger->notice("Run provisioning commands...");
            foreach ($blueprint->getProvision() as $cmd) {
                // ping vbox to avoid timeout
                $this->vbox->getAPIVersion();

                $this->logger->info($cmd);
                $this->logger->info($guest->exec($cmd));
            }

            $this->logger->notice("Set new password...");
            $password = $blueprint->getPassword();
            $this->logger->info("New machine password: vagrant : $password");
            $guest->setPassword($password);

            $this->logger->notice("Configure guest network...");
            $guest->configureNetworkInterfaces($blueprint->getInterfaces());

            $this->logger->notice("Shutdown...");
            try {
                $guest->shutdown();
                $ssh->disconnect();
            } catch (\Throwable $th) {
                // might cause an exception like "Connection closed prematurely"
                $this->logger->info($th->getMessage());
            }

            while ($vm->getState() != "PoweredOff") {
                sleep(2);
            }
            sleep(2);
        } else {
            $this->logger->info("Skip guest config");
        }

        $this->logger->notice("Configure VM network interfaces...");
        $this->configureVMNetwork($vm, $blueprint);

        $this->logger->notice("Boot VM...");
        $vm->launch();
        sleep(2);

        return $vm;
    }


    /**
     *
     * @param VM $vm
     * @param \App\Cyrange\Blueprint $blueprint
     * @throws \Exception
     */
    public function configureVMNetwork(VM $vm, Blueprint $blueprint) : void
    {

        foreach ($blueprint->interfaces() as $i => $interface) {
            $nic = $vm->getNetworkAdapter($i);
            $nic->setEnabled(true);

            $mode = $interface->mode;

            if ($mode === InterfaceBlueprint::BRIDGED) {
                $nic->setAttachmentType("Bridged");
                $nic->setBridgedInterface($interface->network);
            } elseif ($mode == InterfaceBlueprint::VIRTUAL) {
                $nic->setAttachmentType("Internal");
                $nic->setInternalNetwork($interface->network);
            } else {
                throw new \Exception("Invalid network mode: $mode");
            }
        }
    }

    /**
     * Executes $func until it returns true
     *
     * @param callable $func
     * @param int $max
     * @param int $wait seconds before retrying
     * @return boolean
     */
    public function retry(callable $func, int $max = 120, int $wait = 2) : bool
    {
        $iter = 0;
        while ($iter < $max) {
            try {
                if ($func()) {
                    return true;
                }
            } catch (\Exception $ex) {
            }

            sleep($wait);
            $iter++;
        }
        return false;
    }

    /**
     * Find an available port between RDP_START_PORT (15000) and
     * RDP_END_PORT (16999).
     *
     * Ports are tested in random order to avoid collision between concurrent
     * deployments.
     *
     * @return int
     */
    public function findOpenPort() : int
    {
        $used_ports = array();
        foreach ($this->vbox->allVMs() as $vm) {
            if ($vm->getVRDEServer()->isEnabled()
                    && $vm->getVRDEServer()->getPort() >= self::RDP_START_PORT) {
                $used_ports[] = $vm->getVRDEServer()->getPort();
            }
        }

        $port = rand(self::RDP_START_PORT, self::RDP_END_PORT);
        while (in_array($port, $used_ports)) {
            $port = rand(self::RDP_START_PORT, self::RDP_END_PORT);
        }

        return $port;
    }
}


/**
 * Check if haystack starts with needle
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function starts_with($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}
