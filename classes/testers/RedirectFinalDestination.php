<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Testers;

use Vdlp\Redirect\Classes\TesterBase;
use Vdlp\Redirect\Classes\TesterResult;

/**
 * Class RedirectFinalDestination
 *
 * @package Vdlp\Redirect\Classes\Testers
 */
class RedirectFinalDestination extends TesterBase
{
    /**
     * Execute test
     *
     * @return TesterResult
     */
    protected function test(): TesterResult
    {
        $curlHandle = curl_init($this->testUrl);

        $this->setDefaultCurlOptions($curlHandle);

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);

        $error = null;

        if (curl_exec($curlHandle) === false) {
            $error = trans('vdlp.redirect::lang.test_lab.not_determinate_destination_url');
        }

        $finalDestination = curl_getinfo($curlHandle, CURLINFO_REDIRECT_URL);
        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        curl_close($curlHandle);

        if (empty($finalDestination) && $statusCode > 400) {
            $message = $error ?? trans('vdlp.redirect::lang.test_lab.no_destination_url');
        } else {
            $finalDestination = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                e($finalDestination),
                e($finalDestination)
            );

            $message = $error ?? trans('vdlp.redirect::lang.test_lab.final_destination_is', ['destination' => $finalDestination]);
        }

        return new TesterResult($error === null, $message);
    }
}
