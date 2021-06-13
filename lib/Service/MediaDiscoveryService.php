<?php

namespace OCA\AutomaticMediaEncoder\Service;

use OCA\AutomaticMediaEncoder\Traits\NextcloudConfig;
use OC\Files\Node\Node;
use OCP\BackgroundJob\IJobList;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use Psr\Log\LoggerInterface;


class MediaDiscoveryService
{
    use NextcloudConfig;

    public function findNewVideos($userId)
    {
        try {
            $this->userId = $userId;

            $this->logger->info("Finding new media for $this->userId");
    
            if ($this->getConfigValue('find_new_videos_running', '0') === '1') return;
    
            $this->setConfigValue('find_new_videos_running', 1);
        
            foreach ($this->getConversionRules() as $rule) {
                $this->dispatchConversionJobs($rule);
            }
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    private function dispatchConversionJobs($rule)
    {
        $retry = json_decode($this->getConfigValue('retry', '[]'));
        $failed = json_decode($this->getConfigValue('failed', '[]'));
        
        $unconvertedSourceFiles = $this->getUnconvertedSourceFiles($rule['from']['extension'], $rule['to']['extension']);
        
        $queue = json_decode($this->getConfigValue('queue', '[]'));
        $queueableSourceFiles = $this->getQueueableSourceFiles($queue, $unconvertedSourceFiles);
        $this->setConfigValue('queue', array_merge($queue, array_keys($queueableSourceFiles)));

        foreach ($unconvertedSourceFiles as $sourceFileId => $sourceFile) {
            $this->jobList->add(ConvertVideoJob::class, [
                'user_id' => $this->userId, 
                'source_file_id' => $sourceFileId, 
                'rule' => $rule
            ]);
        }
    }

    private function getUnconvertedSourceFiles($fromExtension, $toExtension)
    {
        $sourceFiles = $this->searchUserFolder($fromExtension);
        $convertedSourceFiles = $this->searchUserFolder($toExtension);
        return array_udiff(
            $sourceFiles, 
            $convertedSourceFiles, 
            function (Node $a, Node $b) use ($fromExtension, $toExtension) {
                return $this->sourceFileIsConvertedToExtension($a, $b, $fromExtension, $toExtension) ? 0 : -1;
            }
        );
    }

    private function getQueueableSourceFiles($queue, $unconvertedSourceFiles)
    {
        $newFiles = [];
        foreach ($unconvertedSourceFiles as $unconvertedSourceFile) {
            $fileId = $unconvertedSourceFile->getId();
            if (in_array($fileId, $queue)) continue;
            $newFiles[$fileId] = $unconvertedSourceFile;
        }        
        return $newFiles;
    }

    private function addToCounters($counter, $amount)
    {
        $this->addToAdminCounter($counter, $amount);
        $this->addToCounter($counter, $amount);
    }

    private function addToAdminCounter($counter, $amount) 
    {
        $this->setAdminConfigValue($counter, $this->getAdminConfigValue($counter, '0') + $amount);
    }

    private function addToCounter($counter, $amount) 
    {
        $this->setConfigValue($counter, $this->getConfigValue($counter, '0') + $amount);
    }

    private function sourceFileIsConvertedToExtension(Node $a, Node $b, $fromExtension, $toExtension)
    {
        return $this->getFileName($a->getName()) === $this->getFileName($b->getName())
            && str_replace('.', '', $a->getExtension()) === $fromExtension
            && str_replace('.', '', $b->getExtension()) === $toExtension;
    }

    private function getFileName($filename)
    {
        return substr($filename, 0, strpos($filename, '.'));
    }

    private function searchUserFolder($term)
    {
        return $this->rootFolder->getUserFolder($this->userId)->search(".$term");
    }

    protected function getConversionRules()
    {
        return json_decode($this->getConfigValue("video_conversion_rules", '[]'), true);
    }

    protected function handleError(\Throwable $e)
    {
        $this->logger->warning('Automatic Media Encoder error:  ' . $e->getMessage(), ['app' => $this->appName, 'error' => (string)$e]);
    }
}