<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use BadMethodCallException;
use Cache;
use Carbon\Carbon;
use Illuminate\Cache\TaggedCache;
use Vdlp\Redirect\Classes\Contracts\CacheManagerInterface;
use Vdlp\Redirect\Models\Settings;

/**
 * Class CacheManager
 *
 * Wrapper class for managing redirect cache.
 *
 * @package Vdlp\Redirect\Classes
 */
final class CacheManager implements CacheManagerInterface
{
    private const CACHE_TAG = 'Vdlp.Redirect';

    /**
     * @var TaggedCache
     */
    private $cache;

    /**
     * @param TaggedCache $cache
     */
    public function __construct(TaggedCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        return $this->cache->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function forget(string $key): bool
    {
        return $this->cache->forget($key);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    /**
     * {@inheritDoc}
     */
    public function cacheKey(string $requestPath, string $scheme): string
    {
        // Most caching backend have no limits on key lengths.
        // But to be sure I chose to MD5 hash the cache key.
        return md5($requestPath . $scheme);
    }

    /**
     * {@inheritDoc}
     */
    public function flush(): void
    {
        $this->cache->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function putRedirectRules(array $redirectRules): void
    {
        $this->cache->forever('Vdlp.Redirect.Rules', $redirectRules);
    }

    /**
     * {@inheritDoc}
     */
    public function getRedirectRules(): array
    {
        return (array) $this->cache->get('Vdlp.Redirect.Rules', []);
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * The user has enabled the cache and the current driver supports cache tags.
     *
     * @return bool
     */
    public static function cachingEnabledAndSupported(): bool
    {
        if (!Settings::isCachingEnabled()) {
            return false;
        }

        try {
            Cache::tags([static::CACHE_TAG]);
        } catch (BadMethodCallException $e) {
            return false;
        }

        return true;
    }

    /**
     * The user has enabled the cache, but the current driver does not support cache tags.
     *
     * @return bool
     */
    public static function cachingEnabledButNotSupported(): bool
    {
        if (!Settings::isCachingEnabled()) {
            return false;
        }

        try {
            Cache::tags([static::CACHE_TAG]);
        } catch (BadMethodCallException $e) {
            return true;
        }

        return false;
    }
}
