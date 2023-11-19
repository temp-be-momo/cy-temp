<?php

namespace App;

use Cylab\Guacamole\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * App\VM
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property string $name
 * @property string $uuid
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|VM newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VM newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VM query()
 * @method static \Illuminate\Database\Eloquent\Builder|VM whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VM whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VM whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VM whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VM whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VM whereUuid($value)
 * @mixin \Eloquent
 */
class VM extends Model
{

    protected $dateFormat = 'U';
    protected $table = 'vms';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getUUID() : string
    {
        return $this->uuid;
    }

    public function getState()
    {
        try {
            return $this->getVBoxVM()->getState();
        } catch (\Exception $ex) {
            return "?";
        }
    }

    public function stateBadge() : string
    {
        $state = $this->getState() ;
        switch ($state) {
            case "Running":
                return '<span class="badge bg-success">Running</span>';

            case "PoweredOff":
                return '<span class="badge bg-danger">PoweredOff</span>';
        }
        return '<span class="badge bg-warning">' . $state . '</span>';
    }

    private $vboxvm = null;

    public function getVBoxVM(?bool $force_reload = false) : \Cylab\Vbox\VM
    {
        if ($this->vboxvm === null || $force_reload) {
            $this->vboxvm = VBoxVM::find($this->uuid);
        }

        return $this->vboxvm;
    }
    
    public function totalStorageSize() : int
    {
        $vboxvm = $this->getVBoxVM();
        $size = 0;
        foreach ($vboxvm->getMediumAttachments() as $a) {
            if ($a->hasMedium()) {
                $size += $a->getMedium()->getSize();
            }
        }
        return $size;
    }

    public function hasVBoxVM() : bool
    {
        try {
            $this->getVBoxVM()->getState();
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    public function guacamole() : ?Entity
    {
        if (! $this->hasVBoxVM()) {
            return null;
        }
        $port = $this->getVBoxVM()->getVRDEServer()->getPort();
        return Guacamole::findUserByPort($port);
    }

    public function url() : string
    {
        return action('VMController@show', ['vm' => $this]);
    }

    /**
     * Check if there is a VM for this UUID.
     * @param string $uuid
     * @return bool
     */
    public static function exists(string $uuid) : bool
    {
        return self::findByUUID($uuid) !== null;
    }

    public static function findByUUID(string $uuid) : ?VM
    {
        return self::where('uuid', $uuid)->first();
    }

    public static function find(int $id) : VM
    {
        return self::where('id', $id)->first();
    }

    public static function findByRDPPort(int $port) : ?VM
    {
        foreach (VBoxVM::vbox()->allVMs() as $vm) {
            if ($vm->getVRDEServer()->isEnabled()
                    && $vm->getVRDEServer()->getPort() == $port) {
                $uuid = $vm->getUUID();
                return self::findByUUID($uuid);
            }
        }

        return null;
    }
}
