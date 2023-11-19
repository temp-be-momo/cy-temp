<?php

namespace App\Http\Controllers;

use Cylab\Guacamole\Connection;

class ConnectionController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cylab\Guacamole\Connection  $connection
     */
    public function destroy(Connection $connection)
    {
        $user = $connection->users()->first();
        $connection->delete();

        return redirect(action('AccountController@show', ['account' => $user]));
    }
}
