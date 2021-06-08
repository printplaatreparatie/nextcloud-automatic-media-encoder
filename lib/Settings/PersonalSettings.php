<?php

namespace OCA\AutomaticMediaEncoder\Settings;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\BackgroundJob\IJobList;
use OCP\IConfig;
use OCP\IDateTimeFormatter;
use OCP\IInitialStateService;
use OCP\IL10N;
use OCP\Settings\ISettings;

class PersonalSettings implements ISettings
{
    private string $appName;
    private string $userId;
    private IInitialStateService $initialStateService;
    private IConfig $config;

    public function __construct(string $AppName, string $userId, IConfig $config, IInitialStateService $initialStateService)
    {
        $this->appName = $AppName;
        $this->userId = $userId;
        $this->config = $config;
        $this->initialStateService = $initialStateService;
    }

    public function getForm(): TemplateResponse
    {
        $this->initialStateService->provideInitialState($this->appName, 'admin-config', [
            'status_message' => $this->getUserConfig('status_message', 'Initializing...'),
            'status_error' => $this->getUserConfig('status_error'),
            'video_conversion_enabled' => $this->getUserConfig('video_conversion_enabled', '1'),
            'photo_conversion_enabled' => $this->getUserConfig('photo_conversion_enabled', '1'),
            'audio_conversion_enabled' => $this->getUserConfig('audio_conversion_enabled', '1'),
            'total_unconverted_videos' => $this->getUserConfig('total_unconverted_videos'),
            'total_unconverted_photos' => $this->getUserConfig('total_unconverted_photos'),
            'total_unconverted_audios' => $this->getUserConfig('total_unconverted_audios'),
            'total_converted_videos' => $this->getUserConfig('total_converted_videos'),
            'total_converted_photos' => $this->getUserConfig('total_converted_photos'),
            'total_converted_audios' => $this->getUserConfig('total_converted_audios'),
            'video_conversion_rules' => json_decode($this->getUserConfig('video_conversion_rules', '[]')),
            'photo_conversion_rules' => json_decode($this->getUserConfig('photo_conversion_rules', '[]')),
            'audio_conversion_rules' => json_decode($this->getUserConfig('audio_conversion_rules', '[]')),
            'video_from_formats' => [],
            'video_to_formats' => [],
            'photo_from_formats' => [],
            'photo_to_formats' => [],
            'audio_from_formats' => [],
            'photo_from_formats' => []
        ]);

        return new TemplateResponse(Application::APP_ID, 'user-config');
    }

    public function getSection(): string
    {
        return Application::APP_ID;
    }

    public function getPriority()
    {
        return 50;
    }

    private function getUserConfig($key, $default = '')
    {
        return $this->config->getUserValue($this->userId, Application::APP_ID, $key, $default);
    }
}
