<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Testers;

use Backend;
use Request;
use Vdlp\Redirect\Classes\Exceptions\InvalidScheme;
use Vdlp\Redirect\Classes\Exceptions\RulesPathNotReadable;
use Vdlp\Redirect\Classes\TesterBase;
use Vdlp\Redirect\Classes\TesterResult;

/**
 * Class RedirectMatch
 *
 * @package Vdlp\Redirect\Classes\Testers
 */
class RedirectMatch extends TesterBase
{
    /**
     * {@inheritdoc}
     * @throws InvalidScheme
     */
    protected function test(): TesterResult
    {
        try {
            $manager = $this->getRedirectManager();
        } catch (RulesPathNotReadable $e) {
            return new TesterResult(false, $e->getMessage());
        }

        // TODO: Add scheme.
        $match = $manager->match($this->testPath, Request::getScheme());

        if ($match === false) {
            return new TesterResult(false, trans('vdlp.redirect::lang.test_lab.not_match_redirect'));
        }

        $message = sprintf(
            '%s <a href="%s" target="_blank">%s</a>.',
            trans('vdlp.redirect::lang.test_lab.matched'),
            Backend::url('vdlp/redirect/redirects/update/' . $match->getId()),
            trans('vdlp.redirect::lang.test_lab.redirect')
        );

        return new TesterResult(true, $message);
    }
}
