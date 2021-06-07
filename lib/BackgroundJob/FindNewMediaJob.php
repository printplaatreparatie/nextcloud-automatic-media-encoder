<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use OCP\Notification\IManager as INotificationManager;

class FindNewMediaJob extends BackgroundJob
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
        parent::run($arguments);

        $this->logger->info("Finding new media for $this->userId");

        if ($this->getConfigValue('new_media_seek_running', '0') === '1') {
            return;
        }

        $this->setConfigValue('new_media_seek_running', 1);

        foreach (['video', 'photo', 'audio'] as $mediaType) {
            $rules = $this->getConversionRules($mediaType);

            if ($rules === null) {
                continue;
            }

            foreach ($rules as $rule) {
                $unconvertedMedia = $this->getUnconvertedMedia($mediaType, $rule);

                foreach ($unconvertedMedia as $medium) {
                    $this->{'handle' . ucwords($mediaType) . 'MediumConversion'}($medium, $rule); 
                    $this->handlePostMediumConversion($medium, $rule);
                }
            }
        }
    }

    private function getUnconvertedMedia($mediaType, $rule)
    {
        // so you're giving me a media type and a rule, and you want all the unconverted media?
        
        // k.
        
        // first, i need to declare a return list.
        $returnList = [];
        
        // then, i need to recurse through $rule['source_directory'] 
        
        // if node(file) is converted (record exists in database/config), return, otherwise insert to database/config and include in return list.

        // then...
        return $returnList;
    }

    private function handleVideoMediumConversion($medium, $rule)
    {
        // call ffmpeg 
        // `ffmpeg -i $medium ${medium/*.$rule['source_extension']/.$rule['destination_extension']}`
    }

    private function handleAudioMediumConversion($medium, $rule)
    {
        // call ffmpeg
        // `ffmpeg -i $medium ${medium/*.$rule['source_extension']/.$rule['destination_extension']}`
    }

    private function handlePhotoMediumConversion($medium, $rule)
    {
        // if source format is heic and destination format is jpeg
            // call heif-convert
            // `heif-convert $medium ${medium/*.heic/.jpg}`
        // else
            // unsupported
    }

    private function handlePostMediumConversion($medium, $rule)
    {
        switch ($rule['afterConvertOption']) {
            case 'delete':
                $this->deleteMedium($medium, $rule);
                $this->scanUserFiles();
                break;
            case 'move':
                $this->moveMedium($medium, $rule);
                $this->scanUserFiles();
                break;
            case 'keep':
            default:
                break;
        }
    }

    private function deleteMedium($medium, $rule)
    {   
        // delete file in nextcloud (hopefully triggers physical delete)
    }

    private function moveMedium($medium, $rule)
    {
        // move $medium to $rule['moveMediaDirectory'];
    }    
}
