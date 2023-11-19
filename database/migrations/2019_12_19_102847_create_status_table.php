<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("created_at");
            $table->integer("updated_at")->nullable();
            $table->integer('vm_count');
            $table->integer('web_accounts');
            $table->integer('web_accounts_active');
            $table->double('cpu_load');
            $table->integer('cpu_count');
            $table->integer('memory_used');
            $table->integer('memory_total');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status');
    }
}
