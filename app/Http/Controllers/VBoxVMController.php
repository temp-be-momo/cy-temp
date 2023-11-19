<?php
namespace App\Http\Controllers;

use App\VBoxVM;
use App\VM;
use App\Jobs\HaltAll;
use App\Jobs\UpAll;
use App\Toastr;

use Cylab\Vbox\NetworkAdapter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class VBoxVMController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $vms = [];

        try {
            $vms = VBoxVM::all();
        } catch (\SoapFault $ex) {
            Toastr::error("Failed to get list of VM's. Is VirtualBox webservice "
                . "running? " . $ex->getMessage());
        }
        return view("vbox.index", ["vms" => $vms]);
    }

    public function assign(string $uuid)
    {
        $vboxvm = VBoxVM::find($uuid);

        $vm = new VM();
        $vm->name = $vboxvm->getName();
        $vm->uuid = $uuid;
        $vm->user_id = Auth::user()->id;
        $vm->save();

        return redirect($vm->url());
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(string $uuid)
    {
        $vm = VBoxVM::find($uuid);
        if ($vm->isRunning()) {
            Toastr::error('VM must be powered off!');
            return redirect(action('VMController@show', ['vm' => VM::findByUUID($vm->getUUID())]));
        }

        return view("vbox.edit", ["vboxvm" => $vm]);
    }

    protected function validatorUpdate(array $data)
    {
        return Validator::make($data, [
            // name cannot be modified
            // we only allow to modify the cyberrange-level name of the machine
            // 'name' => 'required|string|regex:/^[a-zA-Z0-9-]+$/|max:40',
            // !! virtualbox allows max 32 vCPU's per VM
            'cpu_count' => 'required|integer|min:1|max:32',
            'cpu_cap' => 'required|integer|min:1|max:100',
            'memory' => 'required|integer|min:64|max:131072'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request

     */
    public function update(Request $request, string $uuid)
    {
        $this->validatorUpdate($request->all())->validate();

        try {
            $vboxvm = VBoxVM::find($uuid);
            $vboxvm->setCPUCount($request->cpu_count);
            $vboxvm->setMemorySize($request->memory);
            $vboxvm->setCPUCap($request->cpu_cap);
        } catch (\SoapFault $ex) {
            Toastr::error("Failed to modify this VM : " . $ex->getMessage());
            return redirect()->back()->withInput();
        }

        Toastr::info('VM updated');
        return redirect(action('VMController@show', ['vm' => VM::findByUUID($uuid)]));
    }

    public function editNetwork(string $uuid, int $slot)
    {
        $vm = VBoxVM::find($uuid);
        
        if ($vm->isRunning()) {
            Toastr::warning("Machine must be off to modify network adapters!");
            return redirect(action('VMController@show', ['vm' => VM::findByUUID($uuid)]));
        }
        
        $interface = $vm->getNetworkAdapter($slot);
        $host_interfaces = VBoxVM::hostInterfaces();
        $internal_networks = VBoxVM::vbox()->getDHCPServers();
        return view("vbox.network", [
            "vboxvm" => $vm,
            "slot" => $slot,
            "interface" => $interface,
            "host_interfaces" => $host_interfaces,
            "internal_networks" => $internal_networks]);
    }

    public function updateNetwork(Request $request, string $uuid, int $slot)
    {
        $vboxvm = VBoxVM::find($uuid);
        $interface = $vboxvm->getNetworkAdapter($slot);
        $interface->setEnabled($request->input("enabled", false));
        
        if ($request->input('mode') == "Bridged") {
            $interface->setAttachmentType(NetworkAdapter::ATTACHEMENT_BRIDGED);
            $interface->setBridgedInterface($request->input("bridge", ""));
        } else {
            $interface->setAttachmentType(NetworkAdapter::ATTACHEMENT_INTERNAL);
            $interface->setInternalNetwork($request->input('internal'));
        }

        Toastr::info('Network interface updated');
        return redirect(action('VMController@show', ['vm' => VM::findByUUID($uuid)]));
    }

    public function reset(string $uuid)
    {
        try {
            VBoxVM::find($uuid)->reset();
        } catch (\SoapFault $ex) {
            Toastr::error(
                "Failed to reset this VM : " . $ex->getMessage()
            );
            return redirect()->back();
        }
        return redirect(action('VMController@show', ['vm' => VM::findByUUID($uuid)]));
    }

    public function up(string $uuid)
    {
        try {
            VBoxVM::find($uuid)->up();
        } catch (\SoapFault $ex) {
            Toastr::error(
                "Failed to start this VM : " . $ex->getMessage()
            );
            return redirect()->back();
        }
        return redirect(action('VMController@show', ['vm' => VM::findByUUID($uuid)]));
    }

    public function halt(string $uuid)
    {
        try {
            VBoxVM::find($uuid)->halt();
        } catch (\SoapFault $ex) {
            Toastr::error(
                "Failed to halt this VM : " . $ex->getMessage()
            );
            return redirect()->back();
        }
        return redirect(action('VMController@show', ['vm' => VM::findByUUID($uuid)]));
    }

    public function kill(string $uuid)
    {
        try {
            VBoxVM::find($uuid)->kill();
        } catch (\SoapFault $ex) {
            Toastr::error(
                "Failed to kill this VM : " . $ex->getMessage()
            );
            return redirect()->back();
        }
        return redirect(action('VMController@show', ['vm' => VM::findByUUID($uuid)]));
    }

    public function haltAll()
    {
        $result = HaltAll::dispatch(Auth::user());
        return redirect(action('JobController@show', ['job' => $result]));
    }

    public function upAll()
    {
        $result = UpAll::dispatch(Auth::user());
        return redirect(action('JobController@show', ['job' => $result]));
    }
}
