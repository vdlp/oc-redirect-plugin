<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Testers;

use Backend;
use Vdlp\Redirect\Classes\Exceptions\InvalidScheme;
use Vdlp\Redirect\Classes\Exceptions\NoMatchForRequest;
use Vdlp\Redirect\Classes\Exceptions\UnableToLoadRules;
use Vdlp\Redirect\Classes\TesterBase;
use Vdlp\Redirect\Classes\TesterResult;
use Vdlp\Redirect\Models\Redirect;

final class RedirectMatch extends TesterBase
{
    protected function test(): TesterResult
    {
        $manager = $this->getRedirectManager();

        try {
            $match = $manager->match(
                $this->testPath,
                $this->secure ? Redirect::SCHEME_HTTPS : Redirect::SCHEME_HTTP
            );
        } catch (NoMatchForRequest | InvalidScheme | UnableToLoadRules) {
            $match = false;
        }

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
