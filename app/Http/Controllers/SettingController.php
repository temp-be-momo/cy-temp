<?php
namespace App\Http\Controllers;

use App\Setting;
use App\VBoxVM;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|regex:/^[a-zA-Z0-9\s-\.]+$/|max:255'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit()
    {
        return view("setting.edit", [
            "host_interfaces" => VBoxVM::hostInterfaces()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function update(Request $request)
    {
        // $this->validator($request->all())->validate();

        Setting::put('net_bridge_default', $request->input('net_bridge_default'));
        \App\Toastr::success("Settings saved!");
        return redirect(action('SettingController@edit'));
    }
}
