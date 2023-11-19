<?php

namespace App\Cyrange;

/**
 * A group a blueprints for a single user.
 *
 * @author tibo
 */
class BlueprintGroup
{
    /**
     *
     * @var string
     */
    public $id;

    public string $email;

    /**
     *
     * @var Blueprint[]
     */
    public array $blueprints;
}
