<?php

namespace App\Services;

use App\Models\Customer;
use App\Interfaces\RedisCacheInterface;
use App\Interfaces\RedisLockInterface;
use App\Interfaces\RedisQueueInterface;

class CustomerService
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

    public function createCustomer(array $data): Customer
    {
        return Customer::create($data);
    }

    public function updateCustomer(Customer $customer, array $data): bool
    {
        return $customer->update($data);
    }

    public function deleteCustomer(Customer $customer): bool
    {
        return $customer->delete();
    }
}