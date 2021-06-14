<?php

namespace OCA\AutomaticMediaEncoder\Controller;

use OCA\AutomaticMediaEncoder\Service\ConfigService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ConfigController extends Controller
{
    private ConfigService $configService;

    public function __construct($AppName, IRequest $request, ConfigService $configService)
    {
        parent::__construct($AppName, $request);
        $this->configService = $configService;
    }

    /**
     * @NoAdminRequired
     */
    public function setConfig(array $values): DataResponse
    {
        if (isset($values['rules'])) {
            $this->configService->setConfigValueJson('rules', $values['rules']);
        }

        return new DataResponse($this->configService->getCurrentUserConfig());
    }

    /**
     * @NoAdminRequired
     */
    public function getStatistics(): DataResponse
    {
        return new DataResponse($this->configService->getCurrentUserStatistics());
    }

    public function getAdminConfig(): DataResponse
    {
        return new DataResponse($this->configService->getAdminConfig());
    }
}
