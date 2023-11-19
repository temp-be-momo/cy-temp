<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * App\Status
 *
 * Represents the status of the Cyber Range server (VM count, web accounts etc.)
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $vm_count
 * @property int $web_accounts
 * @property int $web_accounts_active
 * @property float $cpu_load
 * @property int $cpu_count
 * @property int $memory_used
 * @property int $memory_total
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereCpuCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereCpuLoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereMemoryTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereMemoryUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereVmCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereWebAccounts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereWebAccountsActive($value)
 * @mixin \Eloquent
 */
class Status extends Model
{

    protected $table = 'status';
    protected $dateFormat = 'U';

    public $vbox_version;

    public function parse()
    {
        // host info
        $s = new \Cylab\System\System();
        $this->cpu_load = $s->load5();

        // in database
        $this->vm_count = VM::count();

        // in guacamole
        try {
            $this->web_accounts = \Cylab\Guacamole\User::count();
            $this->web_accounts_active = \Cylab\Guacamole\UserRecord::countActiveConnections();
        } catch (\Illuminate\Database\QueryException $ex) {
        }

        // virtualbox host
        try {
            $host = VBoxVM::vbox()->host();
            $this->cpu_count = $host->processorCoreCount();
            $this->memory_used = $host->memoryUsed();
            $this->memory_total = $host->memorySize();
            $this->vbox_version = str_replace("_", ".", \App\VBoxVM::vbox()->getAPIVersion());
        } catch (\SoapFault $ex) {
        }
    }

    private static $WEEKLY;

    public static function weekly()
    {
        if (is_null(self::$WEEKLY)) {
            $now = time();
            $from = $now - 7 * 24 * 3600;
            self::$WEEKLY = self::where('created_at', '>=', $from)->get();
        }

        return self::$WEEKLY;
    }

    public static function weeklyCpuLoad() : array
    {
        $points = [];
        $status = self::weekly();
        foreach ($status as $s) {
            $points[] = new TimePoint($s->created_at->timestamp * 1000, $s->cpu_load);
        }
        return $points;
    }

    public static function weeklyVMs() : array
    {
        $points = [];
        $status = self::weekly();
        foreach ($status as $s) {
            $points[] = new TimePoint($s->created_at->timestamp * 1000, $s->vm_count);
        }
        return $points;
    }

    public static function weeklyUsers() : array
    {
        $points = [];
        $status = self::weekly();
        foreach ($status as $s) {
            $points[] = new TimePoint($s->created_at->timestamp * 1000, $s->web_accounts_active);
        }
        return $points;
    }

    public static function weeklyMemory() : array
    {
        $points = [];
        $status = self::weekly();
        foreach ($status as $s) {
            $points[] = new TimePoint(
                $s->created_at->timestamp * 1000,
                $s->memory_used / 1024
            );
        }
        return $points;
    }

    public static $MANIFEST;

    public static function manifest() : array
    {
        if (is_null(self::$MANIFEST)) {
            self::$MANIFEST = json_decode(
                file_get_contents(__DIR__ . '/manifest.json'),
                true
            );
        }

        return self::$MANIFEST;
    }

    public static function releaseTag() : string
    {
        return self::manifest()["tag"];
    }

    const LATEST_TAG = "LATEST_TAG";

    public static function latestTag() : string
    {
        return Cache::get(self::LATEST_TAG, "?");
    }
}
