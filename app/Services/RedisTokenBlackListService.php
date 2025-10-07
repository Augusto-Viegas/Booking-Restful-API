<?php

namespace App\Services;

use App\Interfaces\RedisTokenBlacklistInterface;
use Illuminate\Support\Facades\Redis;

class RedisTokenBlackListService implements RedisTokenBlacklistInterface
{
    protected string $prefix = 'jwt:blacklist:';

    public function add(string $token, int $ttlSeconds): void
    {
        Redis::connection('auth')->setex($this->prefix.$token, $ttlSeconds, true);
    }

    public function isBlacklisted(string $token): bool
    {
        return Redis::connection('auth')->exists($this->prefix.$token) > 0;
    }
}