<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->integer('user_id');
            $table->string('name');
            $table->string('uuid');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vms');
    }
}
