<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Description of DeployResult
 *
 * @author tibo
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $time_started
 * @property int|null $time_finished
 * @property int|null $pid
 * @property string|null $vm_uuid
 * @property int $user_id
 * @property string $name
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult whereTimeFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult whereTimeStarted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\JobResult whereVmUuid($value)
 * @mixin \Eloquent
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|JobResult whereType($value)
 */
class JobResult extends Model
{

    const PENDING = "PENDING";
    const RUNNING = "RUNNING";
    const FINISHED = "FINISHED";

    const DEPLOY = "deploy";
    const DESTROY = "destroy";
    const IMPORT = "import";
    const EXPORT = "export";
    const DEPLOY_SCENARIO = "deploy-scenario";

    protected $dateFormat = 'U';

    public function save(array $options = array())
    {
        $result = parent::save($options);
        if (!is_file($this->getLogPathOnDisk())) {
            $this->logger()->notice("Created ...");
        }
        return $result;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function url() : string
    {
        return action('JobController@show', ['job' => $this]);
    }

    public function getLogPathOnDisk()
    {
        $deploys_dir = storage_path("app/deploys");
        if (! is_dir($deploys_dir)) {
            mkdir($deploys_dir);
        }

        return $deploys_dir . sprintf("/job-%'.09d", $this->id) . ".log";
    }

    public function getLog() : string
    {
        return file_get_contents($this->getLogPathOnDisk());
    }

    const LOG_FORMAT = "[%datetime%] %channel%.%level_name%: %message%\n";
    const LOG_DATE_FORMAT = "Y-m-d\TH:i:s";

    public function logger() : Logger
    {
        $logger = new Logger("Job");
        $handler = new StreamHandler($this->getLogPathOnDisk());
        $handler->setFormatter(new \Monolog\Formatter\LineFormatter(self::LOG_FORMAT, self::LOG_DATE_FORMAT));
        $logger->pushHandler($handler);
        return $logger;
    }

    public function status() : string
    {
        if ($this->time_started == null) {
            return self::PENDING;
        }

        if ($this->time_finished == null) {
            return self::RUNNING;
        }

        return self::FINISHED;
    }

    public function isFinished() : bool
    {
        return $this->time_finished !== null;
    }

    public function timeQueued() : Carbon
    {
        return $this->created_at;
    }

    /**
     *
     * @return \Carbon\Carbon
     */
    public function timeStarted() : Carbon
    {
        return Carbon::createFromTimestamp($this->time_started);
    }

    public function timeFinished() : Carbon
    {
        return Carbon::createFromTimestamp($this->time_finished);
    }

    public function executionTime() : string
    {
        return Carbon::createFromTimestamp($this->time_started)
                        ->diffForHumans(
                            Carbon::createFromTimestamp($this->time_finished),
                            CarbonInterface::DIFF_ABSOLUTE
                        );
    }

    /**
     * Find a deploy result from the UUID of the deployed VM.
     *
     * @param string $uuid
     * @return \App\JobResult
     */
    public static function findByUUID(string $uuid) : JobResult
    {
        return self::where("vm_uuid", $uuid)->first();
    }
}
