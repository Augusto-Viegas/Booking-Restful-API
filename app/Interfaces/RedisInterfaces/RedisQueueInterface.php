<?php

namespace App\Interfaces\RedisInterfaces;

interface RedisQueueInterface
{
    public function dispatch(object $job, string $queue = "default"): void;
}