<?php

namespace App\Interfaces;

interface RedisLockInterface
{
    public function acquire(string $key, int $ttlSeconds, callable $callback, int $waitSeconds = 5);
}