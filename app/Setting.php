<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Setting
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValue($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{

    public $timestamps = false;


    public static function defaultBridgeInterface() : string
    {
        return self::getOrDefault('net_bridge_default', 'lo');
    }
    
    public static function setDefaultBridgeInterface(string $if) : void
    {
        self::set('net_bridge_default', $if);
    }

    public static function exists(string $name) : bool
    {
        $setting = self::where('name', $name)->first();
        if ($setting === null) {
            return false;
        }

        return true;
    }

    public static function getOrDefault(string $name, string $default) : string
    {
        $setting = self::where('name', $name)->first();
        if ($setting === null) {
            return $default;
        }
        return $setting->value;
    }
    
    public static function set(string $name, string $value)
    {
        return self::put($name, $value);
    }

    public static function put(string $name, string $value)
    {
        $setting = self::where('name', $name)->first();
        if ($setting === null) {
            $setting = new Setting();
            $setting->name = $name;
        }

        $setting->value = $value;
        $setting->save();
    }
}
