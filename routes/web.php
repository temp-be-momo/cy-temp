<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// https://cylab.be/blog/122/using-https-over-a-reverse-proxy-in-laravel
$app_url = config("app.url");
if (app()->environment('prod') && !empty($app_url)) {
    URL::forceRootUrl($app_url);
    $schema = explode(':', $app_url)[0];
    URL::forceScheme($schema);
}

Route::get('/', function () {
    return redirect('app/vm');
})->name('home');

Route::get('/home', function () {
    return redirect('app/vm');
});

Route::get('app/stats', 'StatusController@dashboard')->name('stats');

Auth::routes(['register' => false]);

Route::get('/guacamole', function () {
    return redirect(config('guac.url'));
})->name('guacamole');

Route::get('/g', function () {
    return view('guacamole.error');
});

Route::get('/images/{image}/{token}/download', 'ImageController@download');

// --------------- ADMINISTRATORS ONLY
Route::middleware(['admin'])->group(function () {

    Route::get('app/status', 'StatusController@status')->name('status');

    // VM management
    // bulk create route must come before resource, otherwize
    // it will be mixed with app/vm/{vm}
    
    // admin can see all VM's
    Route::get('admin/vm', 'VMController@all')->name('vm.all');
    Route::get('app/vm/deploy', 'VMController@create');
    Route::post('app/vm/deploy', 'VMController@store');
    Route::get('app/vm/bulk', 'VMController@bulkCreate');
    Route::post('app/vm/bulk', 'VMController@bulkStore');
    Route::delete('app/vm/{vm}', 'VMController@destroy');
    Route::get('app/vm/{vm}/edit', 'VMController@edit');
    Route::put('app/vm/{vm}', 'VMController@update');
    Route::put('app/vm/{vm}/unmanage', 'VMController@unmanage');
    Route::get('app/vm/{vm}/export', 'VMController@export');
    Route::post('app/vm/{vm}/export', 'VMController@doExport');
    Route::get('app/vm/create/{template}', 'VMController@createFromTemplate');
    Route::get('app/vm/{vm}/log', 'VMController@log');
    Route::post('app/vm/{vm}/guacamole', 'VMController@guacamole');

    // group operations
    Route::get('app/vbox/all/halt', 'VBoxVMController@haltAll');
    Route::get('app/vbox/all/up', 'VBoxVMController@upAll');

    // virtualbox machines management
    Route::get('app/vbox', 'VBoxVMController@index');
    Route::get('app/vbox/{uuid}/edit', 'VBoxVMController@edit');
    Route::put('app/vbox/{uuid}', 'VBoxVMController@update');
    Route::get('app/vbox/{uuid}/net/{slot}/edit', 'VBoxVMController@editNetwork');
    Route::put('app/vbox/{uuid}/net/{slot}', 'VBoxVMController@updateNetwork');
    Route::get('app/vbox/{uuid}/assign', 'VBoxVMController@assign');
    Route::get('app/vbox/{uuid}/reset', 'VBoxVMController@reset');
    Route::get('app/vbox/{uuid}/up', 'VBoxVMController@up');
    Route::get('app/vbox/{uuid}/halt', 'VBoxVMController@halt');
    Route::get('app/vbox/{uuid}/kill', 'VBoxVMController@kill');
    
    // virtualbox networks management
    Route::resource('app/networks', 'NetworkController');

    // guacamole accounts
    Route::resource('app/account', 'AccountController');

    // guacamole connecionts
    Route::delete('app/connection/{connection}', 'ConnectionController@destroy');

    // users management
    Route::resource('app/user', 'UserController');

    // VM templates
    Route::resource('app/templates', 'TemplateController');

    // Deployments
    Route::get('app/jobs', 'JobController@index');
    Route::get('app/jobs/{job}', 'JobController@show');
    Route::get('app/jobs/{job}/job.json', 'JobController@logs');

    // Settings
    Route::get('app/settings', 'SettingController@edit');
    Route::put('app/settings', 'SettingController@update');

    // VM images
    // large image upload
    Route::get('app/images/{image}/upload', 'ImageController@upload');
    Route::post('app/images/{image}/upload', 'ImageController@doUpload');
    Route::get('app/images/{image}/deploy', 'ImageController@deploy');
    Route::post('app/images/{image}/deploy', 'ImageController@doDeploy');
    Route::get('app/images/import', 'ImageController@import');
    Route::post('app/images/import', 'ImageController@doImport');
    Route::get('app/images/import/alpine', 'ImageController@importAlpine');
    Route::post('app/images/{image}/screenshot', 'ImageController@screenshot');
    Route::resource('app/images', 'ImageController');

    // Scenarios
    Route::resource('app/scenarios', 'ScenarioController');
    Route::get('app/scenarios/{scenario}/deploy', 'ScenarioController@deploy');
    Route::post('app/scenarios/{scenario}/deploy', 'ScenarioController@doDeploy');
});

// --------------- AUTHENTICATED USERS
Route::middleware(['auth'])->group(function () {
    Route::get('app/dashboard', function () {
        return redirect('app/vm');
    });

    // VM : list and show
    Route::get('app/vm', 'VMController@index')->name('vm.index');
    // show CO2 summary
    Route::get('app/vm/co2', 'VMController@co2');
    Route::get('app/vm/{vm}', 'VMController@show');
    Route::get('app/vm/{vm}/thumbnail.png', 'VMController@thumbnail');

    // Profile management
    Route::get('app/profile', 'ProfileController@edit');
    Route::put('app/profile/password', 'ProfileController@updatePassword');
});
