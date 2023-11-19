<?php

namespace App;

use Psr\Log\LoggerInterface;

/**
 * Description of Downloader
 *
 * @author tibo
 */
class Downloader
{

    private $logger;
    private $start_time = 0;
    private $previous_progress = 0;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function downloadToFile(string $url, string $local_path)
    {

        $dir = \dirname($local_path);
        if (! \is_dir($dir)) {
            throw new \Exception("Directory $dir does not exist!");
        }

        $ch = curl_init($url);
        $fp = fopen($local_path, "w");

        $proxy = getenv("http_proxy");
        if ($proxy !== null && $proxy !== "") {
            $proxy = trim(str_replace("http://", "", $proxy), '/');
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'progress']);

        $this->start_time = time();

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }



    public function progress(
        $resource,
        $download_size,
        $downloaded,
        $upload_size,
        $uploaded
    ) {

        if ($download_size == 0) {
            return;
        }

        $progress = round($downloaded * 100 / $download_size);

        if ($progress == 100) {
            return;
        }

        if ($progress <= $this->previous_progress) {
            return;
        }

        $this->previous_progress = $progress;
        $elapsed_time = time() - $this->start_time;
        $remaining = round($elapsed_time / $progress * (100 - $progress));

        $this->logger->notice("$progress% ($remaining sec remaining)");
    }
}
