<?php

namespace App\Services;

use App\Interfaces\RedisQueueInterface;

class RedisQueueService implements RedisQueueInterface
{
    public function dispatch(object $job, string $queue = 'default'): void
    {
        //
        dispatch($job->onQueue($queue));
    }
}