<?php

use App\Guacamole;

use Cylab\Guacamole\User as GuacamoleUser;

use Illuminate\Database\Migrations\Migration;

class CreateDefaultGuacamoleUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $email = "cyrange@example.com";
        $password = "admin";

        if (GuacamoleUser::where("email_address", $email)->first() !== null) {
            return;
        }

        Guacamole::createUser($email, $password);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
