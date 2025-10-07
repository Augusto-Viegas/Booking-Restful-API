<?php

namespace App\Interfaces;

interface RedisQueueInterface
{
    public function dispatch(object $job, string $queue = "default"): void;
}