<?php

namespace OCA\AutomaticMediaEncoder\Traits;

use OCA\AutomaticMediaEncoder\Constants\Queues;

trait CustomQueues
{
    use NextcloudConfig;

    private array $pending;
    private array $converting;
    private array $finished;
    private array $retries;
    private array $failed;
    private array $ignored;

    private array $allJobs;

    private function loadQueues()
    {
        $this->pending = $this->getAppConfigValueJson(Queues::Pending);
        $this->converting = $this->getAppConfigValueJson(Queues::Converting);
        $this->finished = $this->getAppConfigValueJson(Queues::Finished);
        $this->retries = $this->getAppConfigValueJson(Queues::Retries);
        $this->failed = $this->getAppConfigValueJson(Queues::Failed);
        $this->ignored = $this->getAppConfigValueJson(Queues::Ignored);
        $this->allJobs = array_reduce([
            $this->pending,
            $this->converting,
            $this->finished,
            $this->retries,
            $this->failed,
            $this->ignored
        ], function ($allJobs, $queue) {
            return $allJobs + $queue;
        }, []);

        return $this;
    }

    private function removeJob($jobId, $queueName)
    {
        unset($this->{$queueName}[$jobId]);

        $this->setAppConfigValueJson($queueName, $this->{$queueName});
    }

    private function addJob($job, $queueName)
    {
        $this->{$queueName}[$job['id']] = $job;

        $this->setAppConfigValueJson($queueName, $this->{$queueName});
    }

    private function moveJob($job, $sourceQueueName, $destinationQueueName)
    {
        if (isset($this->{$destinationQueueName}[$job['id']])) {
            return;
        }

        $job = json_decode(json_encode($job)); // idk just in case

        $this->removeJob($job['id'], $sourceQueueName);
        $this->addJob($job, $destinationQueueName);

        return $job;
    }

    private function findJob($jobId, $queueName = 'allJobs')
    {
        return $this->{$queueName}[$jobId] ?? null;
    }
}
