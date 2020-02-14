<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Testers;

use Backend;
use Request;
use Vdlp\Redirect\Classes\Exceptions\InvalidScheme;
use Vdlp\Redirect\Classes\TesterBase;
use Vdlp\Redirect\Classes\TesterResult;

final class RedirectMatch extends TesterBase
{
    /**
     * {@inheritDoc}
     * @throws InvalidScheme
     */
    protected function test(): TesterResult
    {
        $manager = $this->getRedirectManager();

        // TODO: Add scheme.
        $match = $manager->match($this->testPath, Request::getScheme());

        if ($match === false) {
            return new TesterResult(false, e(trans('vdlp.redirect::lang.test_lab.not_match_redirect')));
        }

        $message = sprintf(
            '%s <a href="%s" target="_blank">%s</a>.',
            e(trans('vdlp.redirect::lang.test_lab.matched')),
            Backend::url('vdlp/redirect/redirects/update/' . $match->getId()),
            e(trans('vdlp.redirect::lang.test_lab.redirect'))
        );

        return new TesterResult(true, $message);
    }
}
