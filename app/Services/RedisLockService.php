<?php

namespace App\Services;

use App\Interfaces\RedisLockInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

class RedisLockService implements RedisLockInterface
{
    /**
     * @param strin $key
     * @param int $ttlSeconds (How long lock will last)
     * @param callable $callback (script code to be executed after acquiring lock)
     * @param int $waitSeconds (How long block will last with lock)
     * @return mixed (callback return)
     * @throws \Exception (Case can't acquire lock)
     */
    public function acquire(string $key, int $ttlSeconds, callable $callback, int $waitSeconds = 5)
    {
        $lock = Cache::lock($key, $ttlSeconds);

        //block() wait until $waitSeconds try to catch lock
        try {
            return $lock->block($waitSeconds, function () use ($lock, $callback){
                try {
                    return $callback();
                } finally {
                    optional($lock)->release();
                }
            });
        } catch(LockTimeoutException) {
            throw new \RuntimeException("This Resource is being used. Try again later");
        }
    }
}