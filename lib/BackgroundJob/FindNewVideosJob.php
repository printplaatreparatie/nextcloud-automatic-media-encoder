<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use OC\Files\Node\Node;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\IJobList;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use OCP\Notification\IManager as INotificationManager;

class FindNewVideosJob extends BackgroundJob
{
    private IJobList $jobList;

    public function __construct(
        string $appName,
        ITimeFactory $timeFactory,
        IConfig $config,
        IL10N $l10n,
        IRootFolder $rootFolder,
        LoggerInterface $logger,
        INotificationManager $notificationManager,
        IJobList $jobList
    ) {
        parent::__construct($timeFactory, $appName, $config, $l10n, $rootFolder, $logger, $notificationManager);

        $this->jobList = $jobList;
    }

    public function run($arguments)
    {
        parent::run($arguments);

        $this->logger->info("Finding new media for $this->userId");

        if ($this->getConfigValue('new_media_seek_running', '0') === '1') {
            return;
        }

        $this->setConfigValue('new_media_seek_running', 1);

        $rules = $this->getConversionRules();

        if ($rules === null) {
            return;
        }

        foreach ($rules as $rule) {
            $this->dispatchConversionJobs($rule);
        }
    }

    private function dispatchConversionJobs($rule)
    {
        $unconvertedVideos = $this->getUnconvertedVideos(
            $rule['from']['extension'], 
            $rule['to']['extension']
        );

        foreach ($unconvertedVideos as $video) {
            $this->jobList->add(ConvertVideojob::class, [
                'user_id' => $this->userId,
                'video_id' => $video->getId(), 
                'rule' => $rule
            ]);
        }
    }

    private function getUnconvertedVideos($fromExtension, $toExtension)
    {
        $sourceVideos = $this->searchUserFolder($fromExtension);
        $convertedVideos = $this->searchUserFolder($toExtension);

        return array_udiff(
            $sourceVideos,
            $convertedVideos,
            fn (Node $a, Node $b) => $this->fileIsConvertedTo($a, $b, $fromExtension, $toExtension) ? 0 : -1
        );
    }

    private function fileIsConvertedTo(Node $a, Node $b, $fromExtension, $toExtension)
    {
        return $this->getFileName($a->getName()) === $this->getFileName($b->getName()) 
               && str_replace('.', '', $a->getExtension()) === $fromExtension
               && str_replace('.', '', $b->getExtension()) === $toExtension;
    }

    private function getFileName($filename)
    {
        return substr($filename, 0, strpos($filename, '.'));
    }

    private function searchUserFolder($term) {
        return $this->rootFolder->getUserFolder($this->userId)->search(".$term");
    }
}
