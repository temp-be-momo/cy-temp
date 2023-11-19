<?php
namespace App\Http\Controllers;

use Cylab\Guacamole\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

/**
 * Controller to manage Guacamole accounts.
 */
class AccountController extends Controller
{

    public function __construct()
    {
        // Uncomment to require authentication
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string|regex:/^[a-zA-Z0-9\s\-\.\_]+$/|max:255',
            'email_address' => 'required|email'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return view(
            "account.index",
            ["accounts" => User::all()->sortBy("username")]
        );
    }

    /**
     * Show the form for creating a new resource.
     * We use the same view for create and update => provide an empty Account.
     *
     */
    public function create()
    {
        return view("account.edit");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = User::create($request->username, $request->password);
        $user->setEmailAddress($request->email_address);
        $user->save();
        return redirect(action('AccountController@index'));
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(User $account)
    {
        return view("account.show", array("account" => $account));
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(User $account)
    {
        return view("account.edit", array("account" => $account));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request

     */
    public function update(Request $request, int $user_id)
    {
        // don't know why, model injection seems not working here...
        /** @var \Cylab\Guacamole\User $user */
        $user = User::find($user_id);
        $this->validator($request->all())->validate();

        $user->setUsername($request->username);
        $user->setEmailAddress($request->email_address);

        if ($request->password !== null) {
            $user->setPassword($request->password);
        }

        $user->update();
        return redirect(action('AccountController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id

     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect(action("AccountController@index"));
    }
}
