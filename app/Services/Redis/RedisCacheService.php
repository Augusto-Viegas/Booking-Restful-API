<?php

namespace App\Services\Redis;

use App\Interfaces\RedisInterfaces\RedisCacheInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Closure;

class RedisCacheService implements RedisCacheInterface
{
    public function remember(string $key, int $seconds, Closure $callback)
    {
        return Cache::remember($key, now()->addSeconds($seconds), $callback);
    }

    public function rememberWithHit(string $key, int $seconds, Closure $callback): array
    {
        $sentinel = new \stdClass();
        $cached = Cache::get($key, $sentinel);

        if($cached !== $sentinel){
            if(is_string($cached) && str_starts_with($cached, 'SERIALIZED:')){
                $cached = unserialize(substr($cached, 11));
            }

            return ['data' => $cached, 'from_cache' => true];
        }

        $value = $callback();

        if($value instanceof Model || $value instanceof Collection){
            $storeValue = 'SERIALIZED:'.serialize($value);
        } else {
            $storeValue = $value;
        }

        Cache::put($key, $storeValue, now()->addSeconds($seconds));

        return ['data' => $value, 'from_cache' => false];
    }

    public function get(string $key, mixed $default = null)
    {
        return Cache::get($key, $default);
    }

    public function put(string $key, mixed $value, int $seconds = 60): void
    {
        Cache::put($key, $value, now()->addSeconds($seconds));
    }
    
    public function has(string $key):bool
    {
        return Cache::has($key);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    public function forgetPattern(string $pattern): void
    {
        $redis = Cache::getRedis();
        $keys = $redis->keys($pattern);
        foreach($keys as $key){
            $redis->del($key);
        }
    }

}