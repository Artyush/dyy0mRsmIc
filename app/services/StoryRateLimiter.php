<?php
namespace app\services;

use yii\web\CacheSession;

final readonly class StoryRateLimiter
{

    public function __construct(private CacheSession $cache)
    {
    }

    public function canPost(string $ip, int $periodSec): bool
    {
        $last = $this->cache->get($this->cacheKey($ip));

        return !$last || (time() - (int)$last) >= $periodSec;
    }

    public function nextAllowedAt(string $ip, int $periodSec): int
    {
        $last = (int) $this->cache->get($this->cacheKey($ip));

        return $last ? $last + $periodSec : time();
    }

    public function markPosted(string $ip, int $periodSec): void
    {
        $this->cache->set($this->cacheKey($ip), time(), $periodSec);
    }

    private function cacheKey(string $ip): string
    {
        return 'story:rate_limiter:' . $ip;
    }
}
