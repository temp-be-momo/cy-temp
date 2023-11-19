<?php
namespace App\Http\Controllers;

use App\Scenario;
use App\ScenarioBlueprint;
use App\Image;
use App\Jobs\DeployScenario;
use App\VBoxVM;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScenarioController extends Controller
{

    public function __construct()
    {
        // Uncomment to require authentication
        // $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|regex:/^[a-zA-Z0-9\s\-\.]+$/|max:255',
            'yaml' => 'string'
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("scenario.index", ["scenarios" => Scenario::all()->sortBy("name")]);
    }

    /**
     * Show the form for creating a new resource.
     * We use the same view for create and update => provide an empty Scenario.
     *
     */
    public function create()
    {
        return view("scenario.edit", [
            "scenario" => new Scenario(),
            "images" => Image::all(),
            "interfaces" => VBoxVM::hostInterfaces()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $scenario = new Scenario();
        $scenario->name = $request->name;
        $scenario->yaml = $request->yaml;
        $scenario->save();
        return redirect(action('ScenarioController@show', ["scenario" => $scenario]));
    }

    /**
     * Display the specified resource.
     */
    public function show(Scenario $scenario)
    {
        return view("scenario.show", ["scenario" => $scenario]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scenario $scenario)
    {
        return view("scenario.edit", [
            "scenario" => $scenario,
            "images" => Image::all(),
            "interfaces" => VBoxVM::hostInterfaces()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scenario $scenario)
    {
        $this->validator($request->all())->validate();

        $scenario->name = $request->name;
        $scenario->yaml = $request->yaml;
        $scenario->save();

        \App\Toastr::success("Scenario saved!");

        return redirect(action('ScenarioController@show', ["scenario" => $scenario]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scenario $scenario)
    {
        $scenario->delete();
        return redirect(action("ScenarioController@index"));
    }

    public function deploy(Scenario $scenario)
    {
        if (! \App\Setting::exists('net_bridge_default')) {
            \App\Toastr::warning("Default bridge interface must be configured before deploying a VM");
            return redirect(action('SettingController@edit'));
        }

        return view('scenario.deploy', ["scenario" => $scenario]);
    }

    public function doDeploy(Scenario $scenario, Request $request)
    {
        if (! \App\Setting::exists('net_bridge_default')) {
            \App\Toastr::warning("Default bridge interface must be configured before deploying a VM");
            return redirect(action('SettingController@edit'));
        }

        $name = $request->get("name");
        $participants = preg_split("/[\s,]+/", $request->participants);
        $blueprint = ScenarioBlueprint::fromScenario($scenario);

        $result = DeployScenario::dispatch(Auth::user(), $blueprint, $participants, $name);
        return redirect(action("JobController@show", ["job" => $result]));
    }
}
