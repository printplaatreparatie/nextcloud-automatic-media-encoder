<?php

namespace OCA\AutomaticMediaEncoder\Service;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCA\AutomaticMediaEncoder\Constants\Formats;
use OCP\IConfig;

class ConfigService
{
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
            'status_message' => $this->getCurrentUserValue('status_message', 'Watching for media'),
            'status_error' => $this->getCurrentUserValue('status_error'),
            'total_queued' => count(json_decode($this->getCurrentUserValue('queue'))),
            'total_converting' => count(json_decode($this->getCurrentUserValue('converting'))),
            'total_converted' => count(json_decode($this->getCurrentUserValue('converted'))),
            'total_failed' => count(json_decode($this->getCurrentUserValue('failed'))),
            'rules' => $this->getCurrentUserJsonValue('rules', '[]'),
            'formats' => Formats::all()
        ];
    }

    public function getAdminConfig()
    {
        return [
            'status_message' => $this->getAppValue('status_message', 'Watching for media'),
            'status_error' => $this->getAppValue('status_error')
        ];
    }

    public function getCurrentUserValue($key, $default = '')
    {
        return $this->config->getUserValue($this->userId, Application::APP_ID, $key, $default);
    }

    public function getCurrentUserJsonValue($key, $default = '')
    {
        return json_decode($this->getCurrentUserValue($key, $default));
    }

    public function getAppValue($key, $default = '')
    {
        return $this->config->getAppValue(Application::APP_ID, $key, $default);
    }

    public function setCurrentUserValue($key, $value)
    {
        $this->config->setUserValue($this->userId, Application::APP_ID, $key, $value);
    }

    public function setCurrentUserValueJson($key, $value)
    {
        $this->setCurrentUserValue($key, json_encode($value));
    }
}
