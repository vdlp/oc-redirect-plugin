<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Carbon\Carbon;
use Illuminate\Cache\TaggedCache;
use Psr\Log\LoggerInterface;
use Throwable;
use Vdlp\Redirect\Classes\Contracts\CacheManagerInterface;
use Vdlp\Redirect\Models\Settings;

final class CacheManager implements CacheManagerInterface
{
    private const CACHE_TAG = 'Vdlp.Redirect';

    /**
     * @var TaggedCache
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(TaggedCache $cache, LoggerInterface $log)
    {
        $this->cache = $cache;
        $this->log = $log;
    }

    public function get(string $key)
    {
        return $this->cache->get($key);
    }

    public function forget(string $key): bool
    {
        return $this->cache->forget($key);
    }

    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    public function cacheKey(string $requestPath, string $scheme): string
    {
        // Most caching backend have no limits on key lengths.
        // But to be sure I chose to MD5 hash the cache key.
        return md5($requestPath . $scheme);
    }

    public function flush(): void
    {
        $this->cache->flush();
        $this->log->info('Vdlp.Redirect: Redirect cache has been flushed.');
    }

    public function putRedirectRules(array $redirectRules): void
    {
        $this->cache->forever('Vdlp.Redirect.Rules', $redirectRules);
    }

    public function getRedirectRules(): array
    {
        return (array) $this->cache->get('Vdlp.Redirect.Rules', []);
    }

    public function putMatch(string $cacheKey, ?RedirectRule $matchedRule = null): ?RedirectRule
    {
        if ($matchedRule === null) {
            $this->cache->forever($cacheKey, false);
            return null;
        }

        $matchedRuleToDate = $matchedRule->getToDate();

        if ($matchedRuleToDate instanceof Carbon) {
            $minutes = $matchedRuleToDate->diffInMinutes(Carbon::now());
            $this->cache->put($cacheKey, $matchedRule, $minutes);
        } else {
            $this->cache->forever($cacheKey, $matchedRule);
        }

        return $matchedRule;
    }

    public function cachingEnabledAndSupported(): bool
    {
        if (!Settings::isCachingEnabled()) {
            return false;
        }

        try {
            $this->cache->tags([static::CACHE_TAG]);
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    public function cachingEnabledButNotSupported(): bool
    {
        if (!Settings::isCachingEnabled()) {
            return false;
        }

        try {
            $this->cache->tags([static::CACHE_TAG]);
        } catch (Throwable $e) {
            return true;
        }

        return false;
    }
}
