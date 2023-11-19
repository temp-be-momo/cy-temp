<?php

namespace Tests\Feature;

// avoid RefreshDatabase because it uses DB transactions,
// which is not compatible with a queue running in another
// process or container
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

use Tests\TestCase;

use App\VM;
use App\Template;
use App\User;
use App\Setting;
use App\Jobs\ImportImage;
use App\Jobs\DeployTemplate;
use App\VBoxVM;
use Illuminate\Support\Facades\Mail;
use App\Mail\MontlySummary;

use Dotenv\Dotenv;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     *
     * @var User
     */
    private $admin;
    
    /**
     *
     * @var User
     */
    private $user;
    
    private $image;
    private $template;

    public function setUp(): void
    {
        $this->createGuacamoleStructure();

        // will run php artisan migrate:fresh
        parent::setUp();
        $this->createUsers();
    }

    public function tearDown(): void
    {
        foreach (VBoxVM::all() as $vm) {
            $vm->destroy();
        }
        
        parent::tearDown();
    }

    private function createGuacamoleStructure()
    {
        echo "\n+ Create Guacamole DB structure";
        $dotenv = Dotenv::create(__DIR__ . "/../../");
        $dotenv->load();

        $host = getenv("GUACAMOLE_HOST");
        $username = getenv("GUACAMOLE_USERNAME");
        $password = getenv("GUACAMOLE_PASSWORD");

        $dsn = 'mysql:host=' . $host . ';port=3306;';
        $pdo = new \PDO($dsn, $username, $password);

        $pdo->exec("DROP DATABASE guacamole;");
        $pdo->exec("CREATE DATABASE guacamole;");
        $pdo->exec("USE guacamole;");

        $sql = file_get_contents(__DIR__ . '/../../001-create-schema.sql');
        $pdo->exec($sql);
    }

    private function createUsers()
    {
        echo "\n+ Create users";
        $admin = new User();
        $admin->name = "Admin";
        $admin->admin = 1;
        $admin->password = 'whatever';
        $admin->email = "admin@example.com";
        $admin->save();
        $this->admin = $admin;

        $user = new User();
        $user->name = "User";
        $user->admin = 0;
        $user->password = Hash::make("whatever");
        $user->email = "user@example.com";
        $user->save();
        $this->user = $user;
    }
    
    /**
     * @group monthly-summary
     */
    public function testMonthlySummary()
    {
        $this->createImageAndTemplate();
        $this->deployAlpineVM();
        
        echo "\n+ Send monthly summary to admin";
        $admin = $this->admin->refresh();
        $vms = $admin->vms;
        Mail::to($admin)->send(new MontlySummary($vms));
    }

    /**
     * @group profile
     */
    public function testProfile()
    {
        $user = $this->user;

        $this->actingAs($user)
                ->visit('/app/profile')
                ->see('user@example.com');

        $this->actingAs($user)
                ->visit('/app/profile')
                ->type('whatever', 'old_password')
                ->type('a-str0ng-passw0rd!', 'password')
                ->type('a-str0ng-passw0rd!', 'password_confirmation')
                ->press('Update password')
                ->see('Password updated!');
    }

    /**
     * @group users
     */
    public function testUsers()
    {
        $this->get('/app/user')
                ->assertResponseStatus(403);

        $this->actingAs($this->user)
                ->get('/app/user')
                ->assertResponseStatus(403);

        $admin = $this->admin;

        // Delete myself
        //$this->actingAs($admin)
        //        ->visit('/app/user')
        //        ->press('Delete')
        //        ->see('Cannot delete yourself');

        // Create user
        $this->actingAs($admin)
                ->visit('/app/user')
                ->see('admin@example.com')
                ->click('New')
                ->seePageIs('/app/user/create')
                ->type('new-user', 'name')
                ->type('newuser@example.com', 'email')
                ->press('Save')
                ->seePageIs('/app/user')
                ->see('newuser@example.com');

        // Modify user
        $this->actingAs($admin)
                ->visit('/app/user')
                ->click('Edit')
                ->type('newemail@example.com', 'email')
                ->press('Save')
                ->seePageIs('/app/user')
                ->see('newemail@example.com');
    }

    /**
     * Configure global site settings, download Alpine image and create a template
     */
    private function createImageAndTemplate()
    {
        $admin = $this->admin;
        
        echo "\n+ Set Default bridge interface";
        Setting::setDefaultBridgeInterface(env('TEST_DEFAULT_BRIDGE_INTERFACE'));
        
        // dispatchNow runs the job sync, in the current process
        ImportImage::dispatchNow(
            'https://cloud.cylab.be/s/xd8JQa4YW9oHWXr/download',
            'Alpine 3.15.4',
            'Alpine 3.15.4',
            $admin
        );

        echo "\n+ Create Alpine template";
        $template = new Template();
        $template->name = "Alpine";
        $template->image_id = 1;
        $template->cpu_count = 1;
        $template->memory = 128;
        $template->need_guest_config = false;
        $template->save();
        
        $this->template = $template;
    }
    
    private function deployAlpineVM()
    {
        echo "\n+ Deploy Alpine VM";
        $admin = $this->admin;
        DeployTemplate::dispatchNow($this->template, $admin, "alpine", $admin->email);
    }

    public function testLoginRequired()
    {
        $this->visit('/')
                ->see('Login');
    }

    /**
     * Deploy a template and manage the VM
     *
     * @group deploy
     * @return void
     */
    public function testDeploy()
    {
        $this->createImageAndTemplate();
        $admin = $this->admin;

        $this->actingAs($admin)
                ->visit('/')
                ->see('My machines');

        // deploy
        $this->actingAs($admin)
                ->visit('/admin/vm')
                ->click('Deploy')
                ->type('test-deploy', 'name')
                ->select('1', 'template_id')
                ->press('Deploy')
                ->see('test-deploy');

        // wait for VM to be deployed in background
        $this->waitForWorker(60);

        $this->actingAs($admin)
                ->visit('/admin/vm')
                ->see('test-deploy');

        // Check we can control the VM from the interface...
        $vm = \App\VM::find(1);

        $this->actingAs($admin)
                ->visit($vm->url())
                ->see('test-deploy')
                ->click('Reset')
                ->see('Running');

        sleep(2);

        $this->actingAs($admin)
                ->visit($vm->url())
                ->see('test-deploy')
                ->click('Kill')
                ->see('PoweredOff');

        // Export to image
        $this->actingAs($admin)
                ->visit($vm->url())
                ->click('Export')
                ->type('export-test', 'name')
                ->type('export-test', 'description')
                ->press('Export');

        $this->waitForWorker(20);

        // check that the image exists
        $this->actingAs($admin)
                ->visit('/app/images')
                ->see('export-test');

        // run VM and check status
        $this->actingAs($admin)
                ->visit($vm->url())
                ->see('test-deploy')
                ->click('Run')
                ->see('Running');

        sleep(2);

        // Fill the status DB with some entries
        $status = new \App\Status();
        $status->parse();
        $status->save();
        sleep(2);

        $status = new \App\Status();
        $status->parse();
        $status->save();
        sleep(2);

        $this->actingAs($admin)
                ->visit('/app/status')
                ->assertResponseOk()
                ->see('Virtual Machines : 1');

        $this->actingAs($admin)
                ->visit($vm->url())
                ->see('test-deploy')
                ->press('Destroy');

        // Wait for job to run in background
        $this->waitForWorker(5);
        $this->actingAs($admin)
                ->visit('/admin/vm')
                ->dontSee('test-deploy');
    }

    /**
     * Test images management : quick deploy, delete etc.
     *
     * @group images
     */
    public function testImages()
    {
        $this->createImageAndTemplate();
        $admin = $this->admin;

        $this->actingAs($admin)
                ->visit('/app/images')
                ->see('Alpine')
                ->click('Alpine')
                ->click('Quick deploy')
                ->type('quick-deploy-test', 'name')
                ->press('Deploy');

        $this->waitForWorker(30);

        $this->actingAs($admin)
                ->visit('/app/vm')
                ->see('quick-deploy-test');
    }

    /**
     * @group bulk-deploy
     */
    public function testBulkDeploy()
    {
        $this->createImageAndTemplate();

        $admin = $this->admin;

        $this->actingAs($admin)
                ->visit('/admin/vm')
                ->click('Bulk deploy')
                ->seePageIs('/app/vm/bulk')
                ->type('bulk-test', 'name')
                ->select('1', 'template_id')
                ->type("user1@example.com\nuser2@example.com", 'emails')
                ->press("Deploy")
                ->seePageIs('/app/jobs')
                ->see('bulk-test-00');

        // wait for VMs to be deployed in background
        $this->waitForWorker(120);

        $this->actingAs($admin)
                ->visit('/admin/vm')
                ->see('bulk-test-00')
                ->see('bulk-test-01');

        foreach (VM::all() as $vm) {
            $this->actingAs($admin)
                    ->visit($vm->url())
                    ->press('Destroy');
        }

        // let jobs run in background
        $this->waitForWorker(10);
        $this->actingAs($admin)
                ->visit('/admin/vm')
                ->dontSee('test-deploy');
    }

    /**
     * Wait for the queue worker to run the job, if we a NOT using the 'sync'
     * QUEUE_CONNECTION
     * @param int $delay
     */
    private function waitForWorker(int $delay)
    {
        if (getenv("QUEUE_CONNECTION") === "sync") {
            sleep(2);
            return;
        }

        sleep($delay);
    }

    public function testScenario()
    {
        $this->createImageAndTemplate();

        $scenario = file_get_contents(__DIR__ . '/scenario.test.yaml');

        $admin = $this->admin;
        $this->actingAs($admin)
                ->visit('/app/scenarios')
                ->click('New')
                ->type('scenario-test', 'name')
                ->type($scenario, 'yaml')
                ->press("Save");

        $this->actingAs($admin)
                ->visit('/app/scenarios')
                ->see('scenario-test');

        $this->actingAs($admin)
                ->visit('/app/scenarios/1')
                ->see('scenario-test')
                ->click('Deploy')
                ->type('sc01', 'name')
                ->type("user01@example.com\nuser02@example.com", "participants")
                ->press('Save');

        $this->waitForWorker(240);

        $this->assertEquals(4, VM::count());
    }
}
