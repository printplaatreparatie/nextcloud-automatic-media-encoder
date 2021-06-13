<?php

namespace OCA\AutomaticMediaEncoder\Traits;

use OCA\AutomaticMediaEncoder\AppInfo\Application;

trait NextcloudConfig
{
    public function getConfigValue($key, $default = '')
    {
        return $this->config->getUserValue($this->userId, Application::APP_ID, $key, $default);
    }

    public function setConfigValue($key, $value)
    {
        $this->config->setUserValue($this->userId, Application::APP_ID, $key, $value);
    }

    public function getAdminConfigValue($key, $default = '')
    {
        return $this->config->getAppValue(Application::APP_ID, $key, $default);
    }

    public function setAdminConfigValue($key, $value)
    {
        $this->config->setAppValue(Application::APP_ID, $key, $value);
    }
}