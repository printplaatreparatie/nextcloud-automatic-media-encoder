<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use OCP\BackgroundJob\QueuedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use OCP\Notification\IManager as INotificationManager;

class ConvertMediaJob extends BackgroundJob
{
    public function __construct(
        string $appName,
        ITimeFactory $timeFactory,
        IConfig $config,
        IL10N $l10n,
        IRootFolder $rootFolder,
        LoggerInterface $logger,
        INotificationManager $notificationManager
    ) {
        parent::__construct($timeFactory, $appName, $config, $l10n, $rootFolder, $logger, $notificationManager);
    }

    public function run($arguments)
    {
        // iterate over unconverted media discovered by FindNewMediaJob
            // start ffmpeg process via php

        // this job should handle timeouts.  if a conversion is happening when the timeout hits, the conversion should stop.
    }
}