<?php

namespace OCA\AutomaticMediaEncoder\Settings;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCA\AutomaticMediaEncoder\Service\ConfigService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings
{
    private string $appName;
    private IInitialStateService $initialStateService;
    private ConfigService $configService;

    public function __construct($AppName, IInitialStateService $initialStateService, ConfigService $configService)
    {
        $this->appName = $AppName;
        $this->initialStateService = $initialStateService;
        $this->configService = $configService;
    }

    public function getForm(): TemplateResponse
    {
        $this->initialStateService->provideInitialState($this->appName, 'admin-config', $this->configService->getAdminConfig());

        return new TemplateResponse(Application::APP_ID, 'adminSettings');
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
