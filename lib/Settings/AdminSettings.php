<?php

namespace OCA\AutomaticMediaEncoder\Settings;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings
{
    private string $appName;
    private IInitialStateService $initialStateService;
    private IConfig $config;

    public function __construct($AppName, IConfig $config, IInitialStateService $initialStateService)
    {
        $this->appName = $AppName;
        $this->config = $config;
        $this->initialStateService = $initialStateService;
    }

    public function getForm(): TemplateResponse
    {
        $this->initialStateService->provideInitialState($this->appName, 'admin-config', [
            'status_message' => $this->getAppValue('status_message', 'Initializing...'),
            'status_error' => $this->getAppValue('status_error'),
            'video_conversion_enabled' => $this->getAppValue('video_conversion_enabled', '1'),
            'photo_conversion_enabled' => $this->getAppValue('photo_conversion_enabled', '1'),
            'audio_conversion_enabled' => $this->getAppValue('audio_conversion_enabled', '1'),
            'total_unconverted_videos' => $this->getAppValue('total_unconverted_videos'),
            'total_unconverted_photos' => $this->getAppValue('total_unconverted_photos'),
            'total_unconverted_audios' => $this->getAppValue('total_unconverted_audios'),
            'total_converted_videos' => $this->getAppValue('total_converted_videos'),
            'total_converted_photos' => $this->getAppValue('total_converted_photos'),
            'total_converted_audios' => $this->getAppValue('total_converted_audios')
        ]);

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

    private function getAppValue($key, $default = '')
    {
        return $this->config->getAppValue(Application::APP_ID, $key, $default);
    }
}
