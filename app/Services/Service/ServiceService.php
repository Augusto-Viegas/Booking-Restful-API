<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Interfaces\RedisInterfaces\RedisCacheInterface;
use App\Interfaces\RedisInterfaces\RedisLockInterface;
use App\Interfaces\RedisInterfaces\RedisQueueInterface;

class ServiceService
{
    protected RedisLockInterface $redisLock;
    protected RedisCacheInterface $redisCache;
    protected RedisQueueInterface $redisQueue;

    public function __construct(RedisLockInterface $redisLock, RedisCacheInterface $redisCache,
                                RedisQueueInterface $redisQueue)
    {
        $this->redisLock = $redisLock;
        $this->redisCache = $redisCache;
        $this->redisQueue = $redisQueue;
    }

    public function createService(array $data): Service
    {
        return Service::create($data);
    }

    public function updateService(Service $service, array $data): bool
    {
        return $service->update($data);
    }

    public function deleteService(Service $service): bool
    {
        return $service->delete();
    }
}