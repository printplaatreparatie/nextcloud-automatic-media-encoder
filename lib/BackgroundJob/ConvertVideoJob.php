<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use FFMpeg\FFMpeg;
use OCP\BackgroundJob\QueuedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\IJobList;
use OCP\Files\File;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use OCP\Notification\IManager as INotificationManager;

class ConvertVideojob extends BackgroundJob
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
        ['user_id' => $userId, 'video_id' => $videoId, 'rule' => $rule] = $arguments;

        $videos = $this->rootFolder->getUserFolder($userId)->getById($videoId);
        /** @var File */
        $originalVideo = $videos instanceof File ? $videos : reset($videos);
        $originalVideoSourcePath = $originalVideo->getStorage()->getLocalFile($originalVideo->getPath());
        
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($originalVideoSourcePath);
        $video->save()
        
        $sharedVideos = is_array($videos) ? array_slice($videos, 1) : [];

        // call ffmpeg 
        // `ffmpeg -i $video ${medium/*.$rule['source_extension']/.$rule['destination_extension']}`

        $option = $rule['afterConvertOption'];

        if ($option === 'keep') {
            return;
        }

        if ($option === 'delete') {
            $this->deleteVideo($video, $rule);
        }

        if ($option === 'move') {
            $this->moveVideo($video, $rule);
        }

        $this->scanUserFiles();
    }

    private function deleteVideo($video, $rule)
    {
        // delete file in nextcloud (hopefully triggers physical delete)
    }

    private function moveVideo($video, $rule)
    {
        // move $video to $rule['moveMediaDirectory'];
    }
}
