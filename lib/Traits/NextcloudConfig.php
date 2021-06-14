<?php

namespace OCA\AutomaticMediaEncoder\Traits;

use OCA\AutomaticMediaEncoder\AppInfo\Application;

trait NextcloudConfig
{
    public function getConfigValue($key, $default = '')
    {
        return $this->config->getUserValue($this->userId, Application::APP_ID, $key, $default);
    }

    public function getConfigValueJson($key, $default = '[]')
    {
        return json_decode($this->getConfigValue($key, $default), true);
    }

    public function setConfigValue($key, $value)
    {
        $this->config->setUserValue($this->userId, Application::APP_ID, $key, $value);
    }

    public function setConfigValueJson($key, $value = [])
    {
        $this->setConfigValue($key, json_encode($value));
    }

    public function getAppConfigValue($key, $default = '')
    {
        return $this->config->getAppValue(Application::APP_ID, $key, $default);
    }

    public function getAppConfigValueJson($key, $default = '[]')
    {
        return json_decode($this->getAppConfigValue($key, $default), true);
    }

    public function setAppConfigValue($key, $value)
    {
        $this->config->setAppValue(Application::APP_ID, $key, $value);
    }

    public function setAppConfigValueJson($key, $value = [])
    {
        $this->setAppConfigValue($key, json_encode($value));
    }

    public function addToCounters($counter, $amount)
    {
        $this->addToAdminCounter($counter, $amount);
        $this->addToCounter($counter, $amount);
    }

    public function addToAdminCounter($counter, $amount)
    {
        $this->setAppConfigValue($counter, $this->getAppConfigValue($counter, '0') + $amount);
    }

    public function addToCounter($counter, $amount)
    {
        $this->setConfigValue($counter, $this->getConfigValue($counter, '0') + $amount);
    }

    public function getConversionRules()
    {
        return json_decode($this->getConfigValue("video_conversion_rules", '[]'), true);
    }

    public function getQueueCount($queueName)
    {
        return count($this->getAppConfigValueJson($queueName));
    }
}
