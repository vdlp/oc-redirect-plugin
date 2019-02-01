<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Contracts;

use Vdlp\Redirect\Classes\RedirectRule;

/**
 * Interface RedirectConditionInterface
 *
 * @package Vdlp\Redirect\Classes\Contracts
 */
interface RedirectConditionInterface
{
    /**
     * Describes the condition.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Whether this redirect condition passes.
     *
     * When a condition passes the redirect will take place, otherwise the
     * request will be handled as any other.
     *
     * @param RedirectRule $rule
     * @param string $requestUri
     * @return bool
     */
    public function passes(RedirectRule $rule, string $requestUri): bool;
}
