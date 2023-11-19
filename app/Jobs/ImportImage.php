<?php

namespace App\Jobs;

use App\User;
use App\Image;
use App\JobResult;
use App\Downloader;

class ImportImage extends JobWithLog
{

    private $url;
    private $name;
    private $description;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $url,
        string $name,
        string $description,
        User $user
    ) {
        $this->url = $url;
        $this->name = $name;
        $this->description = $description;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function doHandle()
    {
        $result = $this->result();
        $result->logger()->info("Downloading from " . $this->url . " ...");

        $image = new Image();
        $image->user_id = $this->user->id;
        $image->name = $this->name;
        $image->description = $this->description . "\n\n"
                . "Downloaded from " . $this->url;
        $image->save();

        $downloader = new Downloader($result->logger());
        $downloader->downloadToFile($this->url, $image->getPathOnDisk());

        $image->hash = hash_file("sha256", $image->getPathOnDisk());
        $image->save();
    }

    public function createJobResultInstance(): JobResult
    {
        $result = new JobResult();
        $result->name = $this->name;
        $result->type = JobResult::IMPORT;
        $result->user_id = $this->user->id;
        return $result;
    }
}
