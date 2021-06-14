<?php

namespace OCA\AutomaticMediaEncoder\Constants;

class Queues
{
    const Pending = 'pending';
    const Converting = 'converting';
    const Finished = 'finished';
    const Retries = 'retries';
    const Failed = 'failed';
    const Ignored = 'ignored';

    const MaxRetries = 3;
    const RetryAfter = 10;
}
