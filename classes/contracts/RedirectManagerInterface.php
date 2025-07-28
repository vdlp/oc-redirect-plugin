<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Contracts;

use Vdlp\Redirect\Classes\Exceptions\InvalidScheme;
use Vdlp\Redirect\Classes\Exceptions\NoMatchForRequest;
use Vdlp\Redirect\Classes\Exceptions\UnableToLoadRules;
use Vdlp\Redirect\Classes\RedirectManagerSettings;
use Vdlp\Redirect\Classes\RedirectRule;

interface RedirectManagerInterface
{
    /**
     * Create an instance of the RedirectManager with a redirect rule.
     */
    public static function createWithRule(RedirectRule $rule): RedirectManagerInterface;

    /**
     * Find a match based on given URL.
     *
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws UnableToLoadRules
     */
    public function match(string $requestPath, string $scheme): RedirectRule;

    /**
     * Redirect with specific rule.
     */
    public function redirectWithRule(RedirectRule $rule, string $requestUri): void;

    /**
     * Get Location URL to redirect to.
     */
    public function getLocation(RedirectRule $rule): ?string;

    /**
     * Get redirect conditions.
     *
     * @return array|RedirectConditionInterface[]
     */
    public function getConditions(): array;

    /**
     * Add a redirect condition.
     */
    public function addCondition(string $conditionClass, int $priority): RedirectManagerInterface;

    public function setSettings(RedirectManagerSettings $settings): RedirectManagerInterface;
}
