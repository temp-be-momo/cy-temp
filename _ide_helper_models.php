<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * Description of GuacamoleUser
 *
 * @author tibo
 * @property int $user_id
 * @property string $username
 * @property mixed $password_hash
 * @property mixed|null $password_salt
 * @property string $password_date
 * @property int $disabled
 * @property int $expired
 * @property string|null $access_window_start
 * @property string|null $access_window_end
 * @property string|null $valid_from
 * @property string|null $valid_until
 * @property string|null $timezone
 * @property string|null $full_name
 * @property string|null $email_address
 * @property string|null $organization
 * @property string|null $organizational_role
 * @property-read \Illuminate\Database\Eloquent\Collection|\Cylab\Guacamole\ConnectionRecord[] $connectionRecords
 * @property-read int|null $connection_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Cylab\Guacamole\Connection[] $connections
 * @property-read int|null $connections_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereAccessWindowEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereAccessWindowStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereDisabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereEmailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereExpired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereOrganizationalRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser wherePasswordDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser wherePasswordHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser wherePasswordSalt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereValidFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GuacamoleUser whereValidUntil($value)
 * @mixin \Eloquent
 */
	class GuacamoleUser extends \Eloquent {}
}

namespace App{
/**
 * App\Image
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUserId($value)
 */
	class Image extends \Eloquent {}
}

namespace App{
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
	class JobResult extends \Eloquent {}
}

namespace App{
/**
 * App\LogEntry
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $user_id
 * @property string $message
 * @property string $level
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogEntry whereUserId($value)
 * @mixin \Eloquent
 */
	class LogEntry extends \Eloquent {}
}

namespace App{
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
 */
	class Setting extends \Eloquent {}
}

namespace App{
/**
 * App\Status
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
	class Status extends \Eloquent {}
}

namespace App{
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
	class Template extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $admin
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\VM[] $vms
 * @property-read int|null $vms_count
 */
	class User extends \Eloquent {}
}

namespace App{
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
 */
	class VM extends \Eloquent {}
}

