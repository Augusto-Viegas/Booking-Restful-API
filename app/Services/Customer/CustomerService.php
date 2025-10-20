<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Interfaces\RedisInterfaces\RedisCacheInterface;
use App\Interfaces\RedisInterfaces\RedisLockInterface;
use App\Interfaces\RedisInterfaces\RedisQueueInterface;

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