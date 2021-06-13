<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use OCA\AutomaticMediaEncoder\Service\MediaDiscoveryService;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;


// runs once a minute and is global scope.
class FindNewVideosJob extends TimedJob
{
    private MediaDiscoveryService $mediaDiscoveryService;

    public function __construct(ITimeFactory $time, MediaDiscoveryService $mediaDiscoveryService) 
    {
        parent::__construct($time);

        $this->mediaDiscoveryService = $mediaDiscoveryService;

        parent::setInterval(60);
    }

    public function run($arguments)
    {
        $this->mediaDiscoveryService->findNewVideos($arguments['uid']);
    }
}
