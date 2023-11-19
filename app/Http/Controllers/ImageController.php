<?php
namespace App\Http\Controllers;

use App\Image;
use App\Jobs\ImportImage;
use App\Jobs\DeployBlueprint;
use App\Setting;

use App\Cyrange\Blueprint;
use App\Cyrange\InterfaceBlueprint;

use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ImageController extends Controller
{

    public function __construct()
    {
        // Uncomment to require authentication
        // $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     */
    public static function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|regex:/^[a-zA-Z0-9\s\-\.]+$/|max:255',
            'description' => 'required|string'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return view("image.index", ["images" => Image::all()->sortBy("name")]);
    }

    /**
     * Show the form for creating a new resource.
     * We use the same view for create and update => provide an empty Image.
     *
     */
    public function create()
    {
        return view("image.edit", ["image" => new Image()]);
    }

    /**
     * Create new image.
     *
     * 2 steps process :
     * 1. create DB entry
     * 2. redirect to /upload to upload file...
     */
    public function store(Request $request)
    {
        self::validator($request->all())->validate();

        $image = new Image();
        $image->name = $request->name;
        $image->description = $request->description;
        $image->user_id = Auth::id();
        $image->save();
        return redirect(action('ImageController@upload', ["image" => $image]));
    }

    public function upload(Image $image)
    {
        return view('image.upload', ["image" => $image]);
    }

    public function doUpload(FileReceiver $receiver, Image $image)
    {
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need

            $file = $save->getFile();

            $original_name = $file->getClientOriginalName();
            // $extension = $file->getClientOriginalExtension();

            Storage::putFileAs(Image::STORAGE_PATH, $file, $image->filename());

            // compute hash
            $image->hash = hash_file("sha256", $image->getPathOnDisk());
            $image->save();

            // delete chunked file
            unlink($file->getPathname());
            return [
                'path' => $image->filename(),
                'name' => $original_name
            ];
        }

        // we are in chunk mode, lets send the current progress
        /** @var \Pion\Laravel\ChunkUpload\Handler\AbstractHandler $handler */
        $handler = $save->handler();
            return response()->json([
                "done" => $handler->getPercentageDone()
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Image $image
     */
    public function show(Image $image)
    {
        return view("image.show", ["image" => $image]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(Image $image)
    {
        return view("image.edit", ["image" => $image]);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, Image $image)
    {
        self::validator($request->all())->validate();

        $image->name = $request->name;
        $image->description = $request->description;
        $image->save();
        return redirect(action('ImageController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        Image::find($id)->delete();
        return redirect(action("ImageController@index"));
    }

    /**
     * show the form to deploy an image (quick deploy).
     * @param Image $image
     */
    public function deploy(Image $image)
    {
        if (! \App\Setting::exists('net_bridge_default')) {
            \App\Toastr::warning("Default bridge interface must be configured before deploying a VM");
            return redirect(action('SettingController@edit'));
        }

        return view("image.deploy", ["image" => $image]);
    }

    /**
     * start deploying an image
     * @param Image $image
     * @param Request $request
     */
    public function doDeploy(Image $image, Request $request)
    {
        if (! \App\Setting::exists('net_bridge_default')) {
            \App\Toastr::warning("Default bridge interface must be configured before deploying a VM");
            return redirect(action('SettingController@edit'));
        }

        $vm_name = $request->get("name");

        $blueprint = new Blueprint();
        $blueprint->setImage($image->getPathForVBox());
        $blueprint->setCpuCap(100);
        $blueprint->setCpuCount($request->get("cpu_count"));
        $blueprint->setMemory($request->get("memory"));
        $blueprint->setGroupName("/cyrange/" . Auth::user()->slug());
        $blueprint->setHostname($vm_name);

        // for the VM name in VirtualBox, we generate a unique random
        // name to avoid directory collision
        // https://gitlab.cylab.be/cylab/cyber-range-manager/-/issues/16
        $blueprint->setName(date('Ymd.His.') . mt_rand(100, 999) . '.' . $vm_name);

        $blueprint->setNeedGuestConfig(false);
        $blueprint->setNeedRdp(true);

        $interface = new InterfaceBlueprint();
        $interface->setMode(InterfaceBlueprint::BRIDGED);
        $interface->network = Setting::defaultBridgeInterface();
        $blueprint->addInterface($interface);

        $result = DeployBlueprint::dispatch($blueprint, Auth::user());

        return redirect(action('JobController@show', ["job" => $result]));
    }

    /**
     * Show the form to import an image (download).
     */
    public function import()
    {
        return view("image.import", ["image" => new Image()]);
    }

    public function doImport(Request $request)
    {
        $url = $request->input('url');
        $name = $request->input('name');
        $description = $request->input('description');

        $result = ImportImage::dispatch($url, $name, $description, Auth::user());
        return redirect($result->url());
    }

    public function importAlpine()
    {
        $url = "https://cloud.cylab.be/s/xd8JQa4YW9oHWXr/download";
        $name = "Alpine 3.15.4";
        $description = "Alpine 3.15.4";

        $result = ImportImage::dispatch($url, $name, $description, Auth::user());

        \App\Toastr::info('Alpine image will be downloaded in background...');

        return redirect($result->url());
    }

    public function download(Image $image, string $token)
    {
        ini_set('max_execution_time', "1800");

        if ($image->token !== $token) {
            abort(404);
        }

        $headers = [
              'Content-Type' => 'application/x-virtualbox-ova',
           ];

        return response()->download(
            $image->getPathOnDisk(),
            $image->filename(),
            $headers
        );
    }

    /**
     * Upload screenshot of VM.
     *
     * @param Image $image
     */
    public function screenshot(Image $image, Request $request)
    {
        $request->validate([
            "screenshot" => ['required', 'file', 'mimes:png','max:1024'] // max file size in kB
        ]);

        $image->screenshot = Storage::putFile('public/images/screenshots', $request->file('screenshot'));
        $image->save();

        \App\Toastr::success("Screenshot saved!");
        return redirect(action('ImageController@show', ['image' => $image]));
    }
}
