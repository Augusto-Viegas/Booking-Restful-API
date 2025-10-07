<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\RedisCacheInterface;
use App\Interfaces\RedisLockInterface;
use App\Interfaces\RedisQueueInterface;
use App\Interfaces\RedisTokenBlacklistInterface;
use App\Services\RedisCacheService;
use App\Services\RedisLockService;
use App\Services\RedisQueueService;
use App\Services\RedisTokenBlackListService;

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