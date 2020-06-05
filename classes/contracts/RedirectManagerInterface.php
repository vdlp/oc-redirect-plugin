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
     *
     * @param RedirectRule $rule
     * @return RedirectManagerInterface
     */
    public static function createWithRule(RedirectRule $rule): RedirectManagerInterface;

    /**
     * Find a match based on given URL.
     *
     * @param string $requestPath
     * @param string $scheme 'http' or 'https'
     * @return RedirectRule
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws UnableToLoadRules
     */
    public function match(string $requestPath, string $scheme): RedirectRule;

    /**
     * Redirect with specific rule.
     *
     * @param RedirectRule $rule
     * @param string $requestUri
     * @return void
     */
    public function redirectWithRule(RedirectRule $rule, string $requestUri): void;

    /**
     * Get Location URL to redirect to.
     *
     * @param RedirectRule $rule
     * @return bool|string
     */
    public function getLocation(RedirectRule $rule);

    /**
     * Get redirect conditions.
     *
     * @return RedirectConditionInterface[]
     */
    public function getConditions(): array;

    /**
     * Add a redirect condition.
     *
     * @param string $conditionClass
     * @param int $priority
     * @return RedirectManagerInterface
     */
    public function addCondition(string $conditionClass, int $priority): RedirectManagerInterface;

    /**
     * @param RedirectManagerSettings $settings
     * @return mixed
     */
    public function setSettings(RedirectManagerSettings $settings): RedirectManagerInterface;
}
