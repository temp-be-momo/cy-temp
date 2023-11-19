<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Status;

class UpdateCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for updates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $json = \file_get_contents("https://artifacts.cylab.be/cyrange-web/app.json");
        $app = \json_decode($json, true);
        $version = $app["version"];
        echo "Latest version: $version\n";

        Cache::put(Status::LATEST_TAG, $version, 3600*24);
    }
}
