<?php

namespace OCA\AutomaticMediaEncoder\Settings;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\BackgroundJob\IJobList;
use OCP\IConfig;
use OCP\IDateTimeFormatter;
use OCP\IL10N;
use OCP\Settings\ISettings;

class PersonalSettings implements ISettings
{
    private IConfig $config;
    private IL10N $l;
    private IDateTimeFormatter $dateTimeFormatter;
    private IJobList $jobList;

    public function __construct(IConfig $config, IL10N $l, IDateTimeFormatter $dateTimeFormatter, IJobList $jobList)
    {
        $this->config = $config;
        $this->l = $l;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->jobList = $jobList;
    }

    public function getForm(): TemplateResponse
    {
        return new TemplateResponse(Application::APP_ID, 'personalSettings', [
            'test' => 'hello world'
        ]);
    }

    public function getSection(): string
    {
        return Application::APP_ID;
    }

    public function getPriority()
    {
        return 50;
    }
}
