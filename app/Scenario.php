<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Symfony\Component\Yaml\Yaml;
use Garden\Schema\Schema;
use Garden\Schema\ValidationException;

class Scenario extends Model
{
    protected $dateFormat = 'U';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->yaml = file_get_contents(__DIR__ . "/Scenario.default.yaml");
    }

    /**
     * Get an array with all validation errors.
     * If count($scenario->validate()) == 0, scenario has no error
     * @return array
     */
    public function validate() : array
    {
        $scenario = Yaml::parse($this->yaml);

        $schema_errors = $this->validateSchema($scenario);
        if (count($schema_errors) > 0) {
            return $schema_errors;
        }

        return array_merge(
            $this->validateImages($scenario),
            $this->validateBridgeInterfaces($scenario)
        );
    }

    private function validateImages(array $scenario) : array
    {
        $errors = [];
        foreach ($scenario["machines"] as $machine) {
            $image_slug = $machine["image"];
            if (Image::whereSlug($image_slug)->count() < 1) {
                $errors[] = "Image $image_slug does not exist.";
            }
        }

        foreach ($scenario["extra_machines"] as $machine) {
            $image_slug = $machine["image"];
            if (Image::whereSlug($image_slug)->count() < 1) {
                $errors[] = "Image $image_slug does not exist.";
            }
        }
        return $errors;
    }

    private function validateBridgeInterfaces($scenario) : array
    {
        $interfaces = array_map(
            function ($interface) {
                return $interface->name();
            },
            VBoxVM::hostInterfaces()
        );
        $interfaces[] = "DEFAULT";

        $errors = [];
        foreach ($scenario["machines"] as $machine) {
            foreach ($machine["interfaces"] as $interface) {
                if ($interface["mode"] == "bridged"
                        && !in_array($interface["bridge_interface"], $interfaces)) {
                    $errors[] = "Host interface " . $interface["bridge_interface"] . " does not exist.";
                }
            }
        }

        foreach ($scenario["extra_machines"] as $machine) {
            foreach ($machine["interfaces"] as $interface) {
                if ($interface["mode"] == "bridged"
                        && !in_array($interface["bridge_interface"], $interfaces)) {
                    $errors[] = "Host interface " . $interface["bridge_interface"] . " does not exist.";
                }
            }
        }

        return $errors;
    }

    private function validateSchema($scenario) : array
    {
        // https://github.com/vanilla/garden-schema
        $schema = Schema::parse([
            'machines:a' => [
                'name:s',
                'image:s',
                'interfaces:a' => [
                    'mode:string' => ["enum" => ['bridged', 'internal', 'private']]
                ],
                'remote_desktop:b?',
                'provision:a?' => 's'
            ],
            'extra_machines:a' => [
                'name:s',
                'image:s',
                'interfaces:a' => [
                    'mode:string' => ["enum" => ['bridged', 'internal', 'private']]
                ],
                'remote_desktop:b?',
                'provision:a?' => 's'
            ]]);

        $errors = [];
        try {
            $schema->validate($scenario);
        } catch (ValidationException $ex) {
            $errors[] = $ex->getMessage();
        }
        return $errors;
    }
}
