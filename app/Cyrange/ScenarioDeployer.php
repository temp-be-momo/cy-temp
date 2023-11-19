<?php

namespace App\Cyrange;

use Cylab\Vbox\VBox;

use Illuminate\Support\Str;

use Psr\Log\LoggerInterface;

/**
 * Description of ScenarioDeployer
 *
 * @author tibo
 */
class ScenarioDeployer
{
    /**
     *
     * @var BlueprintDeployer
     */
    private $blueprint_deployer;

    public function __construct(VBox $vbox, LoggerInterface $logger)
    {
        $this->blueprint_deployer = new BlueprintDeployer($vbox, $logger);
    }

    /**
     *
     * @param array<string, array> $scenario
     * @param string $name
     * @param string[] $participants
     * @param string $teacher
     * @return BlueprintGroup[]
     */
    public function deploy(array $scenario, string $name, array $participants, string $teacher) : array
    {
        // add a unique prefix to scenario name, to be sure VM's will get
        // a unique name
        $name = date('Ymd.His.') . mt_rand(100, 999) . '.' . $name;

        $blueprint_groups = $this->parseVMBlueprints($scenario, $name, $participants, $teacher);
        $blueprint_groups_with_vm = $this->blueprint_deployer->deployAll($blueprint_groups);

        return $blueprint_groups_with_vm;
    }

    /**
     *
     * @param array<string, array> $scenario
     * @param string $name
     * @param array<string> $participants
     * @return BlueprintGroup[]
     */
    public function parseVMBlueprints(
        array $scenario,
        string $name,
        array $participants,
        string $teacher_email
    ) : array {
        $instances = count($participants);
        $groups = [];

        // Parse extra machines
        $group = new BlueprintGroup();
        $group->id = "extra";
        $group->email = $teacher_email;

        $instance = "extra";
        $password = Str::random(8);
        foreach ($scenario["extra_machines"] as $vm_description) {
            $blueprint = $this->parseVMBlueprint(
                $vm_description,
                $name,
                $instance
            );

            $blueprint->setPassword($password);
            $group->blueprints[] = $blueprint;
        }

        $groups[] = $group;

        // Parse students machines
        for ($i = 1; $i <= $instances; $i++) {
            $group = new BlueprintGroup();
            $group->id = (string) $i;
            $group->email = $participants[$i - 1];

            $instance = sprintf("%'02d", $i);
            $password = Str::random(8);

            foreach ($scenario["machines"] as $vm_description) {
                $blueprint = $this->parseVMBlueprint(
                    $vm_description,
                    $name,
                    $instance
                );

                $blueprint->setPassword($password);
                $group->blueprints[] = $blueprint;
            }

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     *
     * @param array<string, mixed> $vm_description
     * @param string $scenario_name
     * @param string $instance
     * @return Blueprint
     */
    public function parseVMBlueprint(
        array $vm_description,
        string $scenario_name,
        string $instance
    ) : Blueprint {

        $blueprint = new Blueprint();

        $blueprint->setGroupName("/cyrange/" . $scenario_name);
        $blueprint->setName($scenario_name . "-" . $instance . "-" . $vm_description["name"]);
        $blueprint->setHostname($vm_description["name"]);

        $blueprint->setImage($vm_description["image"]);

        if (isset($vm_description["remote_desktop"]) && $vm_description["remote_desktop"]) {
            $blueprint->setNeedRdp(true);
        } else {
            $blueprint->setNeedRdp(false);
        }

        if (isset($vm_description["cpu_count"])) {
            $blueprint->setCpuCount($vm_description["cpu_count"]);
        }

        if (isset($vm_description["cpu_cap"])) {
            $blueprint->setCpuCap($vm_description["cpu_cap"]);
        }

        if (isset($vm_description["memory"])) {
            $blueprint->setMemory($vm_description["memory"]);
        }

        if (isset($vm_description["configure_guest"])) {
            $blueprint->setNeedGuestConfig($vm_description["configure_guest"]);
        }

        if (isset($vm_description["provision"])) {
            $blueprint->setProvision($vm_description["provision"]);
        }

        foreach ($vm_description["interfaces"] as $interface_description) {
            $blueprint->addInterface(
                $this->parseInterfaceDescription(
                    $interface_description,
                    $scenario_name,
                    $instance
                )
            );
        }

        return $blueprint;
    }

    /**
     *
     * @param array<string, string> $description
     * @param string $scenario_name
     * @param string $instance
     * @return InterfaceBlueprint
     * @throws \Exception
     */
    public function parseInterfaceDescription(
        array $description,
        string $scenario_name,
        string $instance
    ) : InterfaceBlueprint {

        $if_blueprint = new InterfaceBlueprint();
        $mode = $description["mode"];

        if ($mode === "bridged") {
            $if_blueprint->setMode(InterfaceBlueprint::BRIDGED);
            $if_blueprint->network = $description["bridge_interface"];
        } elseif ($mode == "internal") {
            $if_blueprint->setMode(InterfaceBlueprint::VIRTUAL);
            $if_blueprint->network = $scenario_name . "-" . $description["network_name"];
        } elseif ($mode == "private") {
            $network_name = $scenario_name . "-" . $instance . "-"
                    . $description["network_name"];

            $if_blueprint->setMode(InterfaceBlueprint::VIRTUAL);
            $if_blueprint->network = $network_name;
        } else {
            throw new \Exception("Invalid interface mode: $mode");
        }

        if (! isset($description["address"])) {
            $if_blueprint->setDHCP();
            return $if_blueprint;
        }

        $if_blueprint->address = $description["address"];
        $if_blueprint->mask = $description["mask"];

        if (isset($description["gateway"])) {
            $if_blueprint->gateway = $description["gateway"];
        }

        if (isset($description["dns-nameservers"])) {
            $if_blueprint->dns_nameservers = $description["dns-nameservers"];
        }

        if (isset($description["dns-search"])) {
            $if_blueprint->dns_search = $description["dns-search"];
        }

        return $if_blueprint;
    }
}
