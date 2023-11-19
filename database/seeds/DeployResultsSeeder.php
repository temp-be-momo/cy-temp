<?php


use App\JobResult;
use Illuminate\Database\Seeder;

class DeployResultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $r = new JobResult();
        $r->name = "test-vm-000";
        $r->user_id = 1;
        $r->time_started = time() - 3600;
        $r->time_finished = time() - 123;
        $r->vm_uuid = "qsdfmlkj";
        $r->save();
    }
}
