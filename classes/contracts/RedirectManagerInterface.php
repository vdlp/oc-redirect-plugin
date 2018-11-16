<?php
declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Contracts;

use Vdlp\Redirect\Classes\Exceptions\InvalidScheme;
use Vdlp\Redirect\Classes\RedirectRule;

/**
 * Class RedirectManagerInterface
 *
 * @package Vdlp\Redirect\Classes\Contracts
 */
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
     * @return RedirectRule|false
     * @throws InvalidScheme
     */
    public function match(string $requestPath, string $scheme);

    /**
     * Redirect with specific rule.
     *
     * @param RedirectRule $rule
     * @param string $requestUri
     * @return void
     */
    public function redirectWithRule(RedirectRule $rule, string $requestUri);

    /**
     * Get Location URL to redirect to.
     *
     * @param RedirectRule $rule
     * @return bool|string
     */
    public function getLocation(RedirectRule $rule);
}
