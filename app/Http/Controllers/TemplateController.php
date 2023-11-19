<?php
namespace App\Http\Controllers;

use App\Template;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TemplateController extends Controller
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
            'name' => 'required|string|max:255',
            'image_id' => 'required|exists:images,id',
            // !! virtualbox allows max 32 vCPU's per VM !!
            'cpu_count' => 'required|integer|min:1|max:32',
            'memory' => 'required|integer|min:64|max:65536',
            'boot_delay' => 'required|integer|min:0|max:600',
            'provision' => 'string|nullable',
            'email_note' => 'string|nullable'
        ]);
    }

    /**
     * Display a listing of the resource.
     *

     */
    public function index()
    {
        return view("template.index", array("templates" => Template::all()->sortBy("name")));
    }

    /**
     * Show the form for creating a new resource.
     * We use the same view for create and update => provide an empty Template.
     *

     */
    public function create()
    {
        return view("template.edit", ["template" => new Template()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request

     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $template = new Template();
        $this->parse($request, $template);
        $template->save();
        return redirect(action('TemplateController@show', ["template" => $template]));
    }

    public function parse(Request $request, Template $template) : Template
    {
        $template->name = $request->name;
        $template->image_id = $request->image_id;
        $template->cpu_count = $request->cpu_count;
        $template->memory = $request->memory;
        $template->boot_delay = $request->boot_delay;
        $template->email_note = $request->input('email_note', '');
        if ($template->email_note == null) {
            $template->email_note = '';
        }

        $template->provision = $request->input('provision', '');
        if ($template->provision == null) {
            $template->provision = '';
        }

        $template->need_guest_config = 0;
        if ($request->need_guest_config != null) {
            $template->need_guest_config = 1;
        }

        return $template;
    }

    /**
     * Display the specified resource.
     *
     * @param  Template $template
     */
    public function show(Template $template)
    {
        return view("template.show", ["template" => $template]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Template $template
     */
    public function edit(Template $template)
    {
        return view("template.edit", ["template" => $template]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Template $template
     */
    public function update(Request $request, Template $template)
    {
        $this->validator($request->all())->validate();
        $template = $this->parse($request, $template);
        $template->save();
        return redirect(action('TemplateController@show', ["template" => $template]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id

     */
    public function destroy($id)
    {
        Template::find($id)->delete();
        return redirect(action("TemplateController@index"));
    }
}
