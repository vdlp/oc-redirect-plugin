<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Contracts;

use Vdlp\Redirect\Classes\RedirectRule;

interface CacheManagerInterface
{
    /**
     * Get item from cache storage.
     */
    public function get(string $key): mixed;

    /**
     * Forget item from cache storage.
     */
    public function forget(string $key): bool;

    /**
     * Checks if items exists in cache storage.
     */
    public function has(string $key): bool;

    /**
     * Generate proper cache key.
     */
    public function cacheKey(string $requestPath, string $scheme): string;

    /**
     * Flush cache storage.
     */
    public function flush(): void;

    /**
     * Put Redirect Rules to cache storage.
     */
    public function putRedirectRules(array $redirectRules): void;

    /**
     * Get Redirect Rules from cache storage.
     */
    public function getRedirectRules(): array;

    /**
     * Put the matched rule to cache (null or RedirectRule).
     */
    public function putMatch(string $cacheKey, ?RedirectRule $matchedRule = null): ?RedirectRule;

    /**
     * Whether caching is enabled by setting and supported.
     */
    public function cachingEnabledAndSupported(): bool;

    /**
     * Whether caching is enabled but not supported.
     */
    public function cachingEnabledButNotSupported(): bool;
}
