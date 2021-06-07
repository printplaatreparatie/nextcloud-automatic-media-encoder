<?php

namespace OCA\AutomaticMediaEncoder\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\Notification\IManager as INotificationManager;

use OCA\AutomaticMediaEncoder\Notification\Notifier;

class Application extends App implements IBootstrap
{
    public const APP_ID = 'automaticmediaencoder';

    public function __construct($urlParams = []) 
    {
        parent::__construct(self::APP_ID, $urlParams);

        $container = $this->getContainer();
        $manager = $container->get(INotificationManager::class);
        $manager->registerNotifierService(Notifier::class);
    }

    public function register(IRegistrationContext $context): void {}
    public function boot(IBootContext $context): void {}
}
