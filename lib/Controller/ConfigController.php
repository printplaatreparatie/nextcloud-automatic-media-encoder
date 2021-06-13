<?php

namespace OCA\AutomaticMediaEncoder\Controller;

use OCA\AutomaticMediaEncoder\Service\ConfigService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ConfigController extends Controller
{
    private ConfigService $configService;

    public function __construct($AppName, IRequest $request, ConfigService $configService) {
        parent::__construct($AppName, $request);
        $this->configService = $configService;
    }

    /**
     * @NoAdminRequired
     */
    public function setConfig(array $values): DataResponse
    {
        if (isset($values['rules'])) {
            $this->configService->setCurrentUserValueJson('rules', $values['rules']);
        }

        return $this->getConfig();
    }

    /**
     * @NoAdminRequired
     */
    public function getConfig(): DataResponse
    {
        return new DataResponse([
            'rules' => $this->configService->getCurrentUserConfig()
        ]);
    }

    public function getAdminConfig(): DataResponse
    {
        return new DataResponse([
            'rules' => $this->configService->getAdminConfig()
        ]);
    }
}
