<?php

namespace App;

use Symfony\Component\Yaml\Yaml;

/**
 * Represents a scenario that has been parsed, and ready to be deployed.
 */
class ScenarioBlueprint
{
    /**
     * Id of the parent scenario
     * @var int
     */
    public $id;

    /**
     * Name of the parent scenario
     * @var string
     */
    public $name;

    /**
     * The definition of the scenario, after parsing.
     * @var array
     */
    public $definition;


    /**
     * Build with ::fromScenario() instead
     */
    private function __construct()
    {
    }

    public static function fromScenario(Scenario $scenario) : ScenarioBlueprint
    {
        $blueprint = new self();
        $blueprint->id = $scenario->id;
        $blueprint->name = $scenario->name;

        $definition = Yaml::parse($scenario->yaml);
        $definition = self::parseImages($definition);
        $definition = self::parseInterfaces($definition);
        $blueprint->definition = $definition;

        return $blueprint;
    }


    public static function parseImages(array $scenario) : array
    {
        // we use foreach loop with an assign by reference
        // don't forget to unset the used value after the loop
        // https://www.php.net/manual/en/control-structures.foreach.php
        foreach ($scenario["machines"] as &$machine) {
            $image_slug = $machine["image"];

            /** @var Image $image */
            $image = Image::whereSlug($image_slug)->first();
            $machine["image"] = $image->getPathForVBox();
        }
        unset($machine);


        foreach ($scenario["extra_machines"] as &$machine) {
            $image_slug = $machine["image"];

            /** @var Image $image */
            $image = Image::whereSlug($image_slug)->first();
            $machine["image"] = $image->getPathForVBox();
        }
        unset($machine);

        return $scenario;
    }

    /**
     * For each VM network interface, replace the DEFAULT interface by the
     * actual name of the default bridge interface.
     * @param array $scenario
     * @return array
     */
    public static function parseInterfaces(array $scenario) :array
    {
        // we use foreach loop with an assign by reference
        // don't forget to unset the used value after the loop
        // https://www.php.net/manual/en/control-structures.foreach.php
        foreach ($scenario["machines"] as &$machine) {
            foreach ($machine["interfaces"] as &$interface) {
                if ($interface["mode"] == "bridged"
                        && $interface["bridge_interface"] == "DEFAULT") {
                    $interface["bridge_interface"] = Setting::defaultBridgeInterface();
                }
            }
            unset($interface);
        }
        unset($machine);

        foreach ($scenario["extra_machines"] as &$machine) {
            foreach ($machine["interfaces"] as &$interface) {
                if ($interface["mode"] == "bridged"
                        && $interface["bridge_interface"] == "DEFAULT") {
                    $interface["bridge_interface"] = Setting::defaultBridgeInterface();
                }
            }
            unset($interface);
        }
        unset($machine);

        return $scenario;
    }
}
