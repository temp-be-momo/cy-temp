<?php

namespace App\Http\Controllers;

use App\Toastr;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    /**
     *
     * @param array $data
     */
    protected function validatorPassword(array $data)
    {
        return Validator::make($data, [
            'password' => 'required|confirmed|min:8',
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail($attribute . ' is invalid.');
                    }
                },
            ]
        ]);
    }

    public function updatePassword(Request $request)
    {
        $this->validatorPassword($request->all())->validate();

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();
        Toastr::info('Password updated!');

        // change password for guacamole user
        $guacamole = $user->guacamole();
        if ($guacamole != null) {
            $guacamole->setPassword($request->password);
            $guacamole->save();
            Toastr::info('Password updated for Guacamole account!');
        }

        return redirect(action('ProfileController@edit'));
    }
}
