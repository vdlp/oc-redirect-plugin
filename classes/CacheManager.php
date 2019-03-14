<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use BadMethodCallException;
use Cache;
use Carbon\Carbon;
use Illuminate\Cache\TaggedCache;
use October\Rain\Support\Traits\Singleton;
use Vdlp\Redirect\Models\Settings;

/**
 * Class CacheManager
 *
 * Wrapper class for managing redirect cache.
 *
 * @package Vdlp\Redirect\Classes
 */
class CacheManager
{
    use Singleton;

    const CACHE_TAG = 'Vdlp.Redirect';

    /**
     * @var TaggedCache
     */
    private $cache;

    /**
     * {@inheritdoc}
     * @throws BadMethodCallException
     */
    protected function init()//: void
    {
        $this->cache = Cache::tags([static::CACHE_TAG]);
    }

    /**
     * Get item from cache storage.
     *
     * @param string $cacheKey
     * @return mixed
     */
    public function get($cacheKey)
    {
        return $this->cache->get($cacheKey);
    }

    /**
     * @param $cacheKey
     * @return bool
     */
    public function forget($cacheKey): bool
    {
        return $this->cache->forget($cacheKey);
    }

    /**
     * Checks if items resists in cache storage.
     *
     * @param string $cacheKey
     * @return bool
     */
    public function has($cacheKey): bool
    {
        return $this->cache->has($cacheKey);
    }

    /**
     * Generate proper cache key.
     *
     * Most caching backend have no limits on key lengths.
     * But to be sure I chose to MD5 hash the cache key.
     *
     * @param string $requestPath
     * @param string $scheme
     * @return string
     */
    public function cacheKey($requestPath, $scheme): string
    {
        return md5($requestPath . $scheme);
    }

    /**
     * Clears redirect cache.
     *
     * @return void
     */
    public function flush()//: void
    {
        $this->cache->flush();
    }

    /**
     * @param array $redirectRules
     * @return void
     */
    public function putRedirectRules(array $redirectRules)
    {
        $this->cache->forever('Vdlp.Redirect.Rules', $redirectRules);
    }

    /**
     * @return array
     */
    public function getRedirectRules(): array
    {
        return (array) $this->cache->get('Vdlp.Redirect.Rules', []);
    }

    /**
     * Put matched rule or FALSE to cache.
     *
     * @param string $cacheKey
     * @param RedirectRule|false $matchedRuleOrFalse
     * @return RedirectRule|false
     */
    public function putMatch($cacheKey, $matchedRuleOrFalse)
    {
        if ($matchedRuleOrFalse === false) {
            $this->cache->forever($cacheKey, false);
            return false;
        }

        $matchedRuleToDate = $matchedRuleOrFalse->getToDate();

        if ($matchedRuleToDate instanceof Carbon) {
            $minutes = $matchedRuleToDate->diffInMinutes(Carbon::now());
            $this->cache->put($cacheKey, $matchedRuleOrFalse, $minutes);
        } else {
            $this->cache->forever($cacheKey, $matchedRuleOrFalse);
        }

        return $matchedRuleOrFalse;
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
