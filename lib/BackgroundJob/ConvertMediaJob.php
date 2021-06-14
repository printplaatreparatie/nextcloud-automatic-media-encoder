<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use OCA\AutomaticMediaEncoder\Constants\Queues;
use OCA\AutomaticMediaEncoder\Traits\CustomQueues;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\QueuedJob;
use OCP\Files\File;
use OCP\Files\IRootFolder;
use Psr\Log\LoggerInterface;

class ConvertVideojob extends QueuedJob
{
    use CustomQueues;

    private IRootFolder $rootFolder;
    private LoggerInterface $logger;

    private array $job;
    private string $jobId;
    private string $userId;
    private string $nodeId;
    private array $rule;

    private File $sourceFile;
    private string $sourcePath;
    private string $outputPath;

    public function __construct(ITimeFactory $time, IRootFolder $rootFolder, LoggerInterface $logger)
    {
        parent::__construct($time);

        $this->rootFolder = $rootFolder;
        $this->logger = $logger;
    }

    public function run($arguments)
    {
        $this->job = $arguments['job'];
        $this->jobId = $this->job['id'];
        $this->userId = $this->job['user_id'];
        $this->nodeId = $this->job['node_id'];
        $this->rule = $this->job['rule'];

        $this->convertMedia();
    }

    public function convertMedia()
    {
        $this
            ->loadQueues()
            ->convertSourceFile()
            ->writeConvertedMediaToDestination()
            ->handlePostConversionRule()
            ->finish();
    }

    private function convertSourceFile()
    {
        try {
            $this->job = $this->moveJob($this->job, Queues::Pending, Queues::Converting);

            $this->sourceFile = $this->rootFolder
                ->getUserFolder($this->userId)
                ->getById($this->nodeId)[0];

            $this->sourcePath = $this->sourceFile
                ->getStorage()
                ->getLocalFile($this->sourceFile->getPath());

            $this->outputPath = str_replace(
                $this->sourcePath,
                ".{$this->rule['from_format']['extension']}",
                ".{$this->rule['to_format']['extension']}"
            );

            return $this->callFFmpeg();
        } catch (\Throwable $e) {
            $this->handleFailedConversion($e->getMessage());
            throw $e;
        }
    }

    private function callFFmpeg()
    {
        $this->logger->info("Called FFmpeg: ffmpeg -i $this->sourcePath -c $this->outputPath");

        proc_open(
            "ffmpeg -i $this->sourcePath -c $this->outputPath",
            [
                ['pipe', 'r'],
                ['pipe', 'w'],
                ['pipe', 'w']
            ],
            $pipes,
            dirname(__FILE__),
            null
        );

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        if (!empty($stderr)) {
            $this->logger->error('FFmpeg error: ' . $stderr);
            throw new \Exception($stderr);
        }

        $this->logger->info("FFmpeg converted to $this->outputPath");

        return $this;
    }

    private function handleFailedConversion(string $error)
    {
        $this->job['attempts'] = ($this->job['attempts'] ?? 0) + 1;
        $this->job['last_tried_at'] = date('Y-m-d H:i:s');
        $this->job['errors'] = ($this->job['errors'] ?? []) + [[
            'id' => uniqid(),
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => $error
        ]];

        if ($this->job['attempts'] < Queues::MaxRetries) {
            $this->job = $this->moveJob($this->job, Queues::Converting, Queues::Retries);
        } else {
            $this->job = $this->moveJob($this->job, Queues::Converting, Queues::Failed);
        }
    }

    private function writeConvertedMediaToDestination()
    {
        try {
            $sourceStream = fopen($this->outputPath, 'r');
            $destinationStream = $this->sourceFile
                ->getParent()
                ->newFile(basename($this->outputPath))
                ->fopen('w');

            stream_copy_to_stream($sourceStream, $destinationStream);
        } catch (\Throwable $e) {
            $this->handleFailedConversion($e->getMessage() . ' - ' . $e->getTraceAsString());
        } finally {
            if (is_resource($destinationStream)) {
                fclose($destinationStream);
            }
            if (is_resource($sourceStream)) {
                fclose($sourceStream);
            }
        }

        return $this;
    }

    private function handlePostConversionRule()
    {
        if ($this->rule['post_encode_rule'] === 'delete') {
            return $this->deleteVideo();
        }

        if ($this->rule['post_encode_rule'] === 'move') {
            return $this->moveVideo();
        }

        return $this;
    }

    private function deleteVideo()
    {
        $this->sourceFile->delete();

        return $this;
    }

    private function moveVideo()
    {
        $filename = basename($this->sourcePath);

        $this->sourceFile->move($this->rule['move_media_directory'] . '/' . $filename);

        return $this;
    }

    private function finish()
    {
        $this->job = $this->moveJob($this->job, Queues::Converting, Queues::Finished);
    }
}
