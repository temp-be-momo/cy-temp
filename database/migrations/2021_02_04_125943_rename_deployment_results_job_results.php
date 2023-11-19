<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDeploymentResultsJobResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename("deploy_results", "job_results");

        Schema::table('job_results', function (Blueprint $table) {
            $table->string("type")->default("deploy");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_results', function (Blueprint $table) {
            //
        });
    }
}
