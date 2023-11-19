<?php

namespace App\Http\Controllers;

use App\VBoxVM;
use App\VM;
use App\Toastr;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    public function index()
    {
        $networks = [];
        
        try {
            $vbox = VBoxVM::vbox();
            $networks = $vbox->getDHCPServers();
        } catch (\SoapFault $ex) {
            Toastr::error("Failed to get list of VM's. Is VirtualBox webservice "
                . "running? " . $ex->getMessage());
        }
        return view("networks.index", ["networks" => $networks]);
    }
    
    public function show(string $network)
    {
        $vbox = VBoxVM::vbox();
        $network = $vbox->findDHCPServerByNetworkName($network);
        
        $machines = [];
        foreach ($network->machines() as $vbox_vm) {
            $machines[] = VM::findByUUID($vbox_vm->getUuid());
        }
        
        return view("networks.show", [
            "network" => $network,
            "machines" => $machines]);
    }
    
    public function create()
    {
        return view('networks.edit');
    }
    
    public function store(Request $request)
    {
        try {
            $vbox = VBoxVM::vbox();
            $net = $vbox->createDHCPServer($request->input('name'));
            $net->setConfiguration(
                $request->input('ip'),
                $request->input('mask'),
                $request->input('lower_ip'),
                $request->input('upper_ip')
            );
            $net->enable();
        } catch (\SoapFault $ex) {
            Toastr::error("Failed to create the network: " . $ex->getMessage());
            return redirect(action('NetworkController@index'));
        }
        
        Toastr::success("Network created!");
        return redirect(action('NetworkController@index'));
    }
    
    public function destroy(string $network)
    {
        $vbox = VBoxVM::vbox();
        $net = $vbox->findDHCPServerByNetworkName($network);
        if (count($net->machines()) > 0) {
            Toastr::error("Network has connected machines. Please delete or disconnect "
                    . "them first.");
            return redirect(action("NetworkController@show", ["network" => $network]));
        }
        
        $net->delete();
        Toastr::success("Network deleted!");
        return redirect(action("NetworkController@index"));
    }
}
