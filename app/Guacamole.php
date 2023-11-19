<?php

namespace App;

use App\Mail\WebAccessGranted;
use App\Mail\WebAccessCreated;

use App\Cyrange\Blueprint;
use App\Cyrange\BlueprintDeployer;

use Cylab\Guacamole\User as GuacamoleUser;
use Cylab\Guacamole\Entity;
use Cylab\Guacamole\Connection;

use Cylab\Vbox\VM;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

/**
 * Description of Guacamole
 *
 * @author tibo
 */
class Guacamole
{
    public static function assignVMtoEmail(Blueprint $blueprint, string $email)
    {
        $user = self::findOrCreateUser($email);
        self::assignVMtoUser($blueprint->getVm(), $user);

        Mail::to($email)->send(new WebAccessGranted($blueprint));
    }

    public static function assignVMtoUser(VM $vm, GuacamoleUser $user)
    {
        // enable RDP
        if (! $vm->getVRDEServer()->isEnabled()) {
            $deployer = new BlueprintDeployer(VBoxVM::vbox());
            $port = $deployer->findOpenPort();

            $vm->getVRDEServer()->setEnabled(true);
            $vm->getVRDEServer()->setPort($port);
            $vm->getVRDEServer()->setBindAddress('0.0.0.0');
        }

        // delete old connections
        $port = $vm->getVRDEServer()->getPort();
        foreach (Connection::byPort($port) as $connection) {
            $connection->delete();
        }

        $conn = new Connection();
        $conn->setName($user->getUsername() . "_" . $vm->getName());
        $conn->setProtocol(Connection::RDP);
        $conn->setHost(config('vbox.host'));
        $conn->setPort($port);
        $conn->save();

        $user->addConnection($conn);
    }

    public static function createUser(string $email, string $password) : GuacamoleUser
    {
        // just in case...
        if (GuacamoleUser::where("email_address", $email)->first() !== null) {
            throw new \Exception("User already exists: $email");
        }

        $user = GuacamoleUser::create($email, $password);
        $user->setEmailAddress($email);
        $user->save();

        Mail::to($email)->send(new WebAccessCreated($email, $password));

        return $user;
    }

    public static function findOrCreateUser(string $email) : GuacamoleUser
    {
        $user = GuacamoleUser::byUsername($email);

        if ($user !== null) {
            return $user;
        }

        $password = Str::random(10);
        return self::createUser($email, $password);
    }

    public static function findUserByPort(int $port) : ?Entity
    {
        $connections = Connection::byPort($port);

        if (count($connections) == 0) {
            return null;
        }

        return $connections[0]->users()->first();
    }
}
