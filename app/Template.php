<?php

namespace App;

use App\Cyrange\Blueprint;
use App\Cyrange\InterfaceBlueprint;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Template
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $name
 * @property int $cpu_count
 * @property int $memory
 * @property int $need_guest_config
 * @property string $provision
 * @property string $email_note
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereCpuCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereEmailNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereMemory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereNeedGuestConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereProvision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Template whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $boot_delay
 * @property int $image_id
 * @property-read \App\Image $image
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereBootDelay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Template whereImageId($value)
 */
class Template extends Model
{

    protected $dateFormat = 'U';

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->need_guest_config = 1;
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function getBlueprint() : Blueprint
    {
        $blueprint = new Blueprint();
        $blueprint->setCpuCap(100);
        $blueprint->setCpuCount($this->cpu_count);
        $blueprint->setGroupName("/cyrange");
        $blueprint->setHostname("cylab");
        $blueprint->setImage($this->image->getPathForVBox());
        $blueprint->setMemory($this->memory);
        $blueprint->setName("cylab");
        $blueprint->setNeedRdp(false);
        $blueprint->setPassword(Str::random(10));

        $commands = [];
        foreach (\explode("\n", $this->provision) as $line) {
            $commands[] = trim($line);
        }

        $blueprint->setProvision($commands);
        $blueprint->setNeedGuestConfig((bool) $this->need_guest_config);

        $interface = new InterfaceBlueprint();
        $interface->setMode(InterfaceBlueprint::BRIDGED);
        $interface->network = Setting::defaultBridgeInterface();
        $blueprint->addInterface($interface);

        return $blueprint;
    }
}
