<?php

namespace OCA\AutomaticMediaEncoder\Settings;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCA\AutomaticMediaEncoder\Constants\Formats;
use OCA\AutomaticMediaEncoder\Service\ConfigService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\Settings\ISettings;

class PersonalSettings implements ISettings
{
    private string $appName;
    private IInitialStateService $initialStateService;
    private ConfigService $configService;

    public function __construct(string $AppName, IInitialStateService $initialStateService, ConfigService $configService)
    {
        $this->appName = $AppName;
        $this->initialStateService = $initialStateService;
        $this->configService = $configService;
    }

    public function getForm(): TemplateResponse
    {
        $this->initialStateService->provideInitialState($this->appName, 'user-config', $this->configService->getCurrentUserConfig());

        return new TemplateResponse(Application::APP_ID, 'personalSettings');
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
