<?php

namespace App\Services\Redis;

use App\Interfaces\RedisInterfaces\RedisQueueInterface;

class RedisQueueService implements RedisQueueInterface
{
    public function dispatch(object $job, string $queue = 'default'): void
    {
        //
        dispatch($job->onQueue($queue));
    }
}