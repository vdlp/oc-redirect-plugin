<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Contracts;

use Vdlp\Redirect\Classes\RedirectRule;

interface CacheManagerInterface
{
    /**
     * Get item from cache storage.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Forget item from cache storage.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool;

    /**
     * Checks if items exists in cache storage.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Generate proper cache key.
     *
     * @param string $requestPath
     * @param string $scheme
     * @return string
     */
    public function cacheKey(string $requestPath, string $scheme): string;

    /**
     * Flush cache storage.
     *
     * @return void
     */
    public function flush(): void;

    /**
     * Put Redirect Rules to cache storage.
     *
     * @param array $redirectRules
     * @return void
     */
    public function putRedirectRules(array $redirectRules): void;

    /**
     * Get Redirect Rules from cache storage.
     *
     * @return array
     */
    public function getRedirectRules(): array;

    /**
     * Put the matched rule to cache (null or RedirectRule).
     *
     * @param string $cacheKey
     * @param RedirectRule|null $matchedRule
     * @return RedirectRule|null
     */
    public function putMatch(string $cacheKey, ?RedirectRule $matchedRule = null): ?RedirectRule;

    /**
     * Whether caching is enabled by setting and supported.
     *
     * @return bool
     */
    public function cachingEnabledAndSupported(): bool;

    /**
     * Whether caching is enabled but not supported.
     *
     * @return bool
     */
    public function cachingEnabledButNotSupported(): bool;
}
