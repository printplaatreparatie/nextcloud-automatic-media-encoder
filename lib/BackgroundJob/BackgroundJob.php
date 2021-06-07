<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\QueuedJob;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use OCP\Notification\IManager as INotificationManager;

class BackgroundJob extends QueuedJob
{
    protected string $userId;
    protected IConfig $config;
    protected IL10N $l10n;
    protected IRootFolder $rootFolder;
    protected LoggerInterface $logger;
    protected INotificationManager $notificationManager;

    public function __construct(
        ITimeFactory $timeFactory, 
        string $appName,
        IConfig $config, 
        IL10N $l10n, 
        IRootFolder $rootFolder, 
        LoggerInterface $logger,
        INotificationManager $notificationManager
    ) {
        parent::__construct($timeFactory);
        $this->appName = $appName;
        $this->config = $config;
        $this->l10n = $l10n;
        $this->rootFolder = $rootFolder;
        $this->logger = $logger;
        $this->notificationManager = $notificationManager;
    }

    public function run($arguments)
    {
        $this->userId = $arguments['user_id'];
    }

    public function getConfigValue($key, $default = '')
    {
        return $this->config->getUserValue($this->userId, Application::APP_ID, $key, $default);
    }

    public function setConfigValue($key, $value)
    {
        $this->config->setUserValue($this->userId, Application::APP_ID, $key, $value);
    }

    protected function scanUserFiles()
    {
        return $this->runProcess("php ../../../../occ files:scan $this->userId");
    }

    private function runProcess($cmd)
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

    protected function getConversionRules($type)
    {
        $json = $this->getConfigValue("{$type}_conversion_rules");
        
        if (empty($json)) {
            return null;
        }

        return json_decode($json, true);
    }
}
