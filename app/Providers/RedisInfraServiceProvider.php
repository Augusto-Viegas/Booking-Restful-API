<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\RedisInterfaces\RedisCacheInterface;
use App\Interfaces\RedisInterfaces\RedisLockInterface;
use App\Interfaces\RedisInterfaces\RedisQueueInterface;
use App\Interfaces\RedisInterfaces\RedisTokenBlacklistInterface;
use App\Services\Redis\RedisCacheService;
use App\Services\Redis\RedisLockService;
use App\Services\Redis\RedisQueueService;
use App\Services\Redis\RedisTokenBlackListService;

class RedisInfraServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(RedisCacheInterface::class, RedisCacheService::class);
        $this->app->bind(RedisLockInterface::class, RedisLockService::class);
        $this->app->bind(RedisQueueInterface::class, RedisQueueService::class);
        $this->app->bind(RedisTokenBlacklistInterface::class, RedisTokenBlackListService::class);
    }

    public function boot() {}
}