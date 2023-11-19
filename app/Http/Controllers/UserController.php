<?php

namespace App\Http\Controllers;

use App\User;
use App\Guacamole;
use App\Mail\UserCreated;
use App\Toastr;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        return view('user.index', ['users' => User::all()]);
    }

    /**
     * Show the form for creating a new resource.
     * We use the same view for create and update => provide an empty VM.
     *

     */
    public function create()
    {
        return view("user.edit", ["user" => new User()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $password = Str::random(10);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;

        $user->password = Hash::make($password);
        if ($request->has('administrator')) {
            $user->admin = 1;
        }
        $user->save();

        Mail::to($user->email)->send(new UserCreated($user->email, $password));
        Toastr::info('User created!');

        Guacamole::createUser($user->email, $password);
        Toastr::info('Guacamole account created!');

        return redirect(action('UserController@index'));
    }


    /**
     * Display the specified resource.
     *
     */
    public function show(User $user)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(User $user)
    {
        return view("user.edit", ["user" => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('administrator')) {
            $user->admin = 1;
        }
        $user->save();

        Toastr::info('User updated!');

        return redirect(action('UserController@index'));
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id == Auth::user()->id) {
            Toastr::info('Cannot delete yourself, you fool!');
            return redirect(action('UserController@index'));
        }

        $user->delete();
        return redirect(action('UserController@index'));
    }
}
