<?php

namespace App\Interfaces\RedisInterfaces;

interface RedisLockInterface
{
    public function acquire(string $key, int $ttlSeconds, callable $callback, int $waitSeconds = 5);
}