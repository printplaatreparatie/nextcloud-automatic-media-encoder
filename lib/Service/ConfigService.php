<?php

namespace OCA\AutomaticMediaEncoder\Service;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCA\AutomaticMediaEncoder\Constants\Formats;
use OCA\AutomaticMediaEncoder\Constants\Queues;
use OCA\AutomaticMediaEncoder\Traits\NextcloudConfig;
use OCP\IConfig;

class ConfigService
{
    use NextcloudConfig;

    private string $userId;
    private IConfig $config;

    public function __construct(string $userId, IConfig $config)
    {
        $this->userId = $userId;
        $this->config = $config;
    }

    public function getCurrentUserConfig()
    {
        return [
            'rules' => $this->getConfigValueJson('rules', '[]'),
            'formats' => Formats::all(),
            'statistics' => [
                Queues::Pending => $this->getQueueCount(Queues::Pending),
                Queues::Converting => $this->getQueueCount(Queues::Converting),
                Queues::Finished => $this->getQueueCount(Queues::Finished),
                Queues::Retries => $this->getQueueCount(Queues::Retries),
                Queues::Failed => $this->getQueueCount(Queues::Failed),
                Queues::Ignored => $this->getQueueCount(Queues::Ignored)
            ]
        ];
    }

    public function getAdminConfig()
    {
        return [
            'status_message' => $this->getAppConfigValue('status_message', 'Watching for media'),
            'status_error' => $this->getAppConfigValue('status_error')
        ];
    }
}
