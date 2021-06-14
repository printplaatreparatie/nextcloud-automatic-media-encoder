<?php

namespace OCA\AutomaticMediaEncoder\BackgroundJob;

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;
use Psr\Log\LoggerInterface;
use OC\Files\Node\Node;
use OCA\AutomaticMediaEncoder\Constants\Queues;
use OCA\AutomaticMediaEncoder\Traits\CustomQueues;
use OCP\Files\File;

class FindNewMediaJob extends TimedJob
{
    use CustomQueues;

    private string $userId;

    public function __construct(ITimeFactory $time, LoggerInterface $logger)
    {
        parent::__construct($time);

        $this->logger = $logger;

        parent::setInterval(60);
    }

    public function run($arguments)
    {
        try {
            $this->userId = $arguments['uid'];

            $this
                ->loadQueues()
                ->findNewVideos();
        } catch (\Throwable $e) {
            $this->logger->warning(
                'Automatic Media Encoder error:  ' . $e->getMessage(),
                [
                    'app' => $this->appName,
                    'error' => (string)$e
                ]
            );
        }
    }

    private function findNewVideos()
    {
        $this->logger->info("Finding new media for $this->userId");

        if ($this->getConfigValue('find_new_videos_running', '0') === '1') {
            $this->logger->info("Another FindNewMediaJob is running for $this->userId");
            return;
        }

        $this->setConfigValue('find_new_videos_running', 1);

        foreach ($this->getConversionRules() as $rule) {
            $this->dispatchConversionJobs($rule);
        }
    }

    private function dispatchConversionJobs($rule)
    {
        $unconvertedNodes = $this->findUnconvertedNodes($rule['from']['extension'], $rule['to']['extension']);
        $queueableNodes = $this->filterQueueableNodesForRule($unconvertedNodes, $rule);

        foreach ($queueableNodes as $node) {
            $job = $this->dispatchConversionJob($node, $rule);

            $this->logger->info("Dispatched conversion job: " . json_encode($job));
        }

        $retryJobs = $this->getRetryableJobsForRule($rule);

        foreach ($retryJobs as $job) {
            $job = $this->retryConversionJob($job, $rule);

            $this->logger->info("Retrying conversion job: " . json_encode($job));
        }
    }

    private function findUnconvertedNodes($fromExtension, $toExtension): array
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

    private function searchUserFolder($term)
    {
        return $this->rootFolder->getUserFolder($this->userId)->search(".$term");
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

    private function filterQueueableNodesForRule($unconvertedNodes, $rule)
    {
        return array_filter($unconvertedNodes, function (Node $node) use ($rule) {
            return $this->isNodeQueueableForRule($node, $rule);
        });
    }

    private function isNodeQueueableForRule(Node $node, $rule): bool
    {
        $id = $node->getId();

        return false === current(array_filter($this->allJobs, function ($job) use ($rule, $id) {
            return $job['rule']['id'] === $rule['id'] && $job['node_id'] === $id;
        }));
    }

    private function dispatchConversionJob(File $node, $rule)
    {
        $job = [
            'id' => uniqid(),
            'user_id' => $this->userId,
            'node_id' => $node->getId(),
            'rule' => $rule,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->addJob($job, Queues::Pending);

        $this->jobList->add(ConvertVideoJob::class, ['job' => $job]);

        return $job;
    }

    private function retryConversionJob($job)
    {
        $this->moveJob($job, Queues::Retries, Queues::Pending);

        $this->jobList->add(ConvertVideoJob::class, ['job' => $job]);

        return $job;
    }

    private function getRetryableJobsForRule($rule)
    {
        return array_filter($this->retries, function ($job) use ($rule) {
            return $job['rule']['id'] === $rule['id']
                && ($job['attempts'] ?? 0) < Queues::MaxRetries
                && strtotime('-' . Queues::RetryAfter . ' minutes') > strtotime($job['last_tried_at']);
        });
    }
}
