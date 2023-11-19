<?php

use App\Image;

use Illuminate\Support\Str;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImageAddHashSlug extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string("hash", 256)->nullable();
            $table->string('slug', 256)->nullable();
        });

        foreach (Image::all() as $image) {
            $image->slug = Str::slug($image->name);
            $image->hash = hash_file("sha256", $image->getPathOnDisk());
            $image->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('hash');
            $table->dropColumn('slug');
        });
    }
}
