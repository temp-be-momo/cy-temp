<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
 * @mixin \Eloquent
 */
class Image extends Model
{

    protected $dateFormat = 'U';

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->token = Str::random(32);
    }

    public function save(array $options = []) : bool
    {
        $this->slug = Str::slug($this->name);
        return parent::save($options);
    }

    const STORAGE_PATH = "images";

    public function getPathOnDisk() : string
    {
        $images_directory = storage_path("app/" . self::STORAGE_PATH);
        if (! is_dir($images_directory)) {
            mkdir($images_directory);
        }

        return $images_directory . "/" . $this->filename();
    }

    public function filename() : string
    {
        return sprintf("image-%'.09d", $this->id) . ".ova";
    }

    /**
     *
     * @return string
     */
    public function getPathForVBox() : string
    {
        $images_directory = config('vbox.images');
        return $images_directory . "/" . $this->filename();
    }

    public function exists() : bool
    {
        return is_file($this->getPathOnDisk());
    }

    public function size() : int
    {
        if (!$this->exists()) {
            return 0;
        }
        return filesize($this->getPathOnDisk());
    }

    public function sizeForHumans() : string
    {
        return $this->humanFilesize($this->size());
    }

    public function humanFilesize($bytes, $decimals = 2) : string
    {
        $sz = 'BKMGTP';
        $factor = (int) floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public function delete()
    {
        try {
            \unlink($this->getPathOnDisk());
        } catch (\Exception $ex) {
            Log::alert("Failed to delete image file " . $this->getPathOnDisk() .
                    " : " . $ex->getMessage());
        }
        return parent::delete();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function downloadURL() : string
    {
        return action(
            'ImageController@download',
            ["image" => $this, "token" => $this->token]
        );
    }
}
