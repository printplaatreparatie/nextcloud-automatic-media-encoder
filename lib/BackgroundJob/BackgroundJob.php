<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\QueuedJob;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use Psr\Log\LoggerInterface;

class BackgroundJob extends QueuedJob
{
    protected string $userId;
    protected IConfig $config;
    protected IRootFolder $rootFolder;
    protected LoggerInterface $logger;

    public function __construct(
        ITimeFactory $timeFactory, 
        string $appName,
        IConfig $config, 
        IRootFolder $rootFolder, 
        LoggerInterface $logger
    ) {
        parent::__construct($timeFactory);
        $this->appName = $appName;
        $this->config = $config;
        $this->rootFolder = $rootFolder;
        $this->logger = $logger;
    }

    public function run($arguments)
    {
        $this->userId = $arguments['user_id'];
    }

    protected function runProcess($cmd)
    {
        $process = proc_open(
            $cmd,
            [
                ['pipe', 'r'],
                ['pipe', 'w'],
                ['pipe', 'w']
            ],
            $pipes,
            dirname(__FILE__),
            null
        );

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        return [
            'process' => $process,
            'stdout' => $stdout,
            'stderr' => $stderr
        ];
    }



    protected function handleError(\Throwable $e)
    {
        $this->logger->warning('Automatic Media Encoder error:  ' . $e->getMessage(), ['app' => $this->appName, 'error' => (string)$e]);
    }
}
