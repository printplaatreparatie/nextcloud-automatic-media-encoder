<?php

namespace OCA\AutomaticMediaEncoder\Controllers;

use OCA\AutomaticMediaEncoder\AppInfo\Application;
use OCP\App\IAppManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\Files\IAppData;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IServerContainer;
use OCP\IURLGenerator;
use Psr\Log\LoggerInterface;

class ConfigController extends Controller
{
    private $userId;
    private $config;

    public function __construct(
        $AppName,
        IRequest $request,
        IServerContainer $serverContainer,
        IConfig $config,
        IAppData $appData,
        IDBConnection $dbConnection,
        IURLGenerator $urlGenerator,
        IL10N $l,
        LoggerInterface $logger,
        $userId
    ) {
        parent::__construct($AppName, $request);
        $this->l = $l;
        $this->appName = $AppName;
        $this->userId = $userId;
        $this->appData = $appData;
        $this->serverContainer = $serverContainer;
        $this->config = $config;
        $this->dbConnection = $dbConnection;
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
    }

    /**
     * @NoAdminRequired
     */
    public function setConfig(array $values): DataResponse
    {
        foreach (['video', 'photo', 'audio'] as $mediaType) {
            $conversionRulesKey = "{$mediaType}_conversion_rules";
            if (isset($values[$conversionRulesKey])) {
                $this->config->setUserValue($this->userId, Application::APP_ID, $conversionRulesKey, $values[$conversionRulesKey]);
            }
        }

        return new DataResponse();
    }
}
