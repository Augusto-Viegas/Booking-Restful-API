<?php

namespace App\Interfaces;

use Closure;

interface RedisCacheInterface
{
    public function remember(string $key, int $seconds, Closure $callback);
    public function rememberWithHit(string $key, int $seconds, Closure $callback): array;
    public function get(string $key, $default = null);
    public function put(string $key, $value, int $seconds): void;
    public function has(string $key): bool;
    //public function exists(string $key): bool;
    public function forget(string $key): void;
    public function forgetPattern(string $pattern): void;
}
