<?php

namespace App\Services;

use App\Interfaces\RedisCacheInterface;
use Illuminate\Support\Facades\Cache;
use Closure;

class RedisCacheService implements RedisCacheInterface
{
    public function remember(string $key, int $seconds, Closure $callback)
    {
        return Cache::remember($key, now()->addSeconds($seconds), $callback);
    }

    public function rememberWithHit(string $key, int $seconds, Closure $callback): array
    {
        //Check if the value is not null
        $sentinel = new \stdClass();
        $value = Cache::get($key, $sentinel);

        if($value !== $sentinel){
            return ['value' => $value, 'from_cache' => true];
        }

        $value = $callback();
        Cache::put($key, $value, now()->addSeconds($seconds));

        return ['value' => $value, 'from_cache' => false];
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