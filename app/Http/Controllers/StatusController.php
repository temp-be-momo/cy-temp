<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function dashboard()
    {
        $status = new \App\Status();
        $status->parse();

        return view('statistics', ['status' => $status]);
    }

    public function status(Request $request)
    {
        $status = new \App\Status();
        $status->parse();

        return view('status', ['status' => $status]);
    }
}
