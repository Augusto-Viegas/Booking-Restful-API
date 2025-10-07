<?php

namespace App\Interfaces;

interface RedisTokenBlacklistInterface
{
    public function add(string $token, int $ttlSeconds): void;
    public function isBlacklisted(string $token): bool;
    //public function has(string $token): bool;
}