<?php

namespace App\Jobs;

use App\User;
use App\VM;
use App\Guacamole;
use App\VBoxVM;
use App\ScenarioBlueprint;
use App\JobResult;
use App\Cyrange\ScenarioDeployer;

/**
 * Description of DeployScenario
 *
 * @author tibo
 */
class DeployScenario extends JobWithLog
{

    /**
     *
     * @var ScenarioBlueprint
     */
    protected $scenario;

    /**
     *
     * @var array
     */
    protected $participants;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var User
     */
    protected $user;


    public function __construct(
        User $user,
        ScenarioBlueprint $scenario,
        array $participants,
        string $name
    ) {

        $this->user = $user;
        $this->scenario = $scenario;
        $this->participants = $participants;
        $this->name = $name;
    }

    protected function doHandle()
    {
        $result = $this->result();
        $logger = $result->logger();

        $vbox = VBoxVM::connect();

        $deployer = new ScenarioDeployer($vbox, $logger);
        $groups = $deployer->deploy(
            $this->scenario->definition,
            $this->name,
            $this->participants,
            $this->user->email
        );

        $this->createVMs($groups);
        $this->createGuacamoleConnections($groups);
    }

    /**
     *
     * @param \App\Cyrange\BlueprintGroup[] $groups
     */
    public function createVMs(array $groups)
    {
        foreach ($groups as $group) {
            foreach ($group->blueprints as $blueprint) {
                /** @var \App\Cyrange\Blueprint $blueprint */
                $vm = new VM();
                $vm->user_id = $this->user->id;
                $vm->name = $blueprint->getName();
                $vm->uuid = $blueprint->getVM()->getUUID();
                $vm->save();
            }
        }
    }

    /**
     *
     * @param array $groups
     */
    public function createGuacamoleConnections(array $groups)
    {
        $logger = $this->result()->logger();

        foreach ($groups as $group) {
            $email = $group->email;
            $logger->notice("Create guacamole access for $email ...");
            foreach ($group->blueprints as $blueprint) {
                Guacamole::assignVMtoEmail($blueprint, $email);
            }
        }
    }

    public function createJobResultInstance(): JobResult
    {
        $result = new JobResult();
        $result->name = $this->name;
        $result->type = JobResult::DEPLOY_SCENARIO;
        $result->user_id = $this->user->id;
        return $result;
    }
}
