<?php
namespace App\Http\Controllers;

use App\VM;
use App\Template;
use App\Guacamole;
use App\Jobs\DeployTemplate;
use App\Jobs\DestroyVM;
use App\Jobs\ExportVM;
use App\Toastr;
use App\Setting;

use Cylab\Guacamole\User as GuacamoleUser;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class VMController extends Controller
{

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorDeploy(array $data)
    {
        return Validator::make($data, [
            'name' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\-]+$/',
                'max:40'],
            'web_access_email' => 'email'
        ]);
    }

    /**
     * Display a listing of the resource.
     *

     */
    public function index()
    {
        $vms = Auth::user()->vms()->orderBy('name')->get();
        $status = \App\VMSummary::fromVMList($vms);

        return view("vm.index", [
            "vms" => $vms,
            "status" => $status]);
    }

    public function all()
    {
        $vms = VM::orderBy('name')->get();
        $status = \App\VMSummary::fromVMList($vms);

        return view("vm.all", [
            "vms" => $vms,
            "status" => $status]);
    }


    /**
     * Show the form for creating a new resource.
     * We use the same view for create and update => provide an empty VM.
     *

     */
    public function create()
    {
        if (! Setting::exists('net_bridge_default')) {
            Toastr::warning("Default bridge interface must be configured before deploying a VM");
            return redirect(action('SettingController@edit'));
        }

        return view(
            "vm.deploy",
            [
                "user" => Auth::user(),
                "template_id" => null]
        );
    }

    public function createFromTemplate(Template $template)
    {
        if (! Setting::exists('net_bridge_default')) {
            Toastr::warning("Default bridge interface must be configured before deploying a VM");
            return redirect(action('SettingController@edit'));
        }

        return view(
            "vm.deploy",
            [
                "user" => Auth::user(),
                "template_id" => $template->id]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request

     */
    public function store(Request $request)
    {
        if (! Setting::exists('net_bridge_default')) {
            Toastr::warning("Default bridge interface must be configured before deploying a VM");
            return redirect(action('SettingController@edit'));
        }

        $this->validatorDeploy($request->all())->validate();

        /** @var Template $template */
        $template = Template::findOrFail($request->template_id);
        $user = Auth::user();
        $vm_name = $request->name;
        $guacamole_email = null;
        if ($request->has("web_access") && $request->has('web_access_email')) {
            $guacamole_email = $request->get('web_access_email', null);
        }

        $result = DeployTemplate::dispatch($template, $user, $vm_name, $guacamole_email);
        return redirect(action('JobController@show', ["job" => $result]));
    }

    protected function validatorBulk(array $data)
    {
        return Validator::make($data, [
            // max size: single VM - 3 characters : '-01' will be appended
            'name' => 'required|string|regex:/^[a-zA-Z0-9-]+$/|max:37',
            'emails' => 'required|string'
        ]);
    }

    public function bulkCreate()
    {
        if (! Setting::exists('net_bridge_default')) {
            Toastr::warning("Default bridge interface must be configured before deploying a VM");
            return redirect(action('SettingController@edit'));
        }

        return view('vm.bulkdeploy');
    }

    public function bulkStore(Request $request)
    {
        if (! Setting::exists('net_bridge_default')) {
            Toastr::warning("Default bridge interface must be configured before deploying a VM");
            return redirect(action('SettingController@edit'));
        }

        $this->validatorBulk($request->all())->validate();

        $template = Template::findOrFail($request->template_id);
        $user = Auth::user();

        $emails = preg_split("/[\s,]+/", $request->emails);
        $names = $this->findAvailableNames($request->name, count($emails));

        for ($i = 0; $i < count($emails); $i++) {
            $guacamole_email = trim($emails[$i]);
            $vm_name = $names[$i];

            DeployTemplate::dispatch(
                $template,
                $user,
                $vm_name,
                $guacamole_email
            );
        }

        return redirect(action('JobController@index'));
    }

    public function findAvailableNames(string $prefix, int $count) : array
    {
        $vms = VM::all();
        $names_taken = $this->extractVmName($vms);
        $names_available = [];
        $i = 0;
        while (count($names_available) < $count) {
            $next = $prefix . "-" . sprintf('%02d', $i);
            if (in_array($next, $names_taken)) {
                $i++;
                continue;
            }

            $names_available[] = $next;
            $names_taken[] = $next;
        }

        return $names_available;
    }

    public function extractVmName($vms) : array
    {
        $names = [];
        foreach ($vms as $vm) {
            /** @var \Cylab\Vbox\VM $vm */
            $names[] = $vm->getName();
        }
        return $names;
    }

    public function log(VM $vm)
    {
        return redirect(action('JobController@show', [
            'job' => \App\JobResult::findByUUID($vm->getUUID())]));
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(VM $vm)
    {
        $this->authorize("show", $vm);

        return view("vm.show", [
            "vm" => $vm,
            "vboxvm" => $vm->getVBoxVM(),
            "guac_users" => GuacamoleUser::all()]);
    }

    /**
     * Remove a VM from the database, without destroying the VM itself.
     *
     * @param Request $request
     * @param VM $vm
     */
    public function unmanage(Request $request, VM $vm)
    {
        $vm->delete();

        Toastr::info('VM removed from DB');
        return redirect(action('VMController@index'));
    }

    public function thumbnail(VM $vm)
    {
        $this->authorize("show", $vm);

        if (!$vm->getVBoxVM()->isRunning()) {
            return response()->file(public_path() . '/images/vm-off.jpg');
        }

        // the max width of img inside card body inside col-md-6 is 498px
        $data = $vm->getVBoxVM()->takeScreenshot(498);
        return response($data)->header('Content-type', 'image/png');
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Request $request, VM $vm)
    {
        $result = DestroyVM::dispatch($vm, Auth::user());

        Toastr::info('VM will be destroyed in background!');
        return redirect(action("VMController@index"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  VM $vm
     */
    public function edit(VM $vm)
    {
        return view("vm.edit", ["vm" => $vm]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            "name" => 'required|string|regex:/^[a-zA-Z0-9\-\._]+$/|max:40',
            "user_id" => 'required|int|exists:users,id'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  VM $vm
     */
    public function update(Request $request, VM $vm)
    {
        $this->validator($request->all())->validate();
        $vm->name = $request->input('name');
        $vm->user_id = $request->input('user_id');
        $vm->save();
        return redirect($vm->url());
    }

    public function export(Request $request, VM $vm)
    {
        if ($vm->getVBoxVM()->isRunning()) {
            Toastr::error('VM must be shutdown to export!');
            return redirect()->back();
        }

        return view("vm.export", [
            "vm" => $vm,
            "name" => $vm->name . "-" . date("Ymd-His")]);
    }

    public function doExport(Request $request, VM $vm)
    {
        ImageController::validator($request->all())->validate();

        $name = $request->input("name");
        $description = $request->input("description");

        ExportVM::dispatch($vm, $name, $description, Auth::user());

        Toastr::info('VM will be exported in background!');
        return redirect(action('VMController@show', ["vm" => $vm]));
    }

    /**
     * Assign a VM to a guacamole user (create the guacamole connection)
     *
     * @param Request $request
     * @param VM $vm
     */
    public function guacamole(Request $request, VM $vm)
    {
        if ($vm->getVBoxVM()->isRunning()) {
            Toastr::error('VM must be shutdown to create guacamole connection!');
            return redirect()->back();
        }

        $user_id = $request->input("guac_user");
        $user = GuacamoleUser::where("user_id", $user_id)->first();
        Guacamole::assignVMtoUser($vm->getVBoxVM(), $user);

        Toastr::info('VM assigned to Guacamole user');
        return redirect(action('VMController@show', ["vm" => $vm]));
    }
    
    /**
     * Show CO2 summary for my machines.
     */
    public function co2()
    {
        $vms = Auth::user()->vms()->get();
        $summary = \App\VMSummary::fromVMList($vms);
        
        return view('vm.co2', ["vms" => $vms, "summary" => $summary]);
    }
}
