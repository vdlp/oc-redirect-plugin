<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Testers;

use Vdlp\Redirect\Classes\Exceptions\InvalidScheme;
use Vdlp\Redirect\Classes\Exceptions\NoMatchForRequest;
use Vdlp\Redirect\Classes\Exceptions\UnableToLoadRules;
use Vdlp\Redirect\Classes\TesterBase;
use Vdlp\Redirect\Classes\TesterResult;
use Vdlp\Redirect\Models\Redirect;

/**
 * Tester for checking if the response HTTP code is equal to the matched redirect.
 *
 * Situations:
 * a) Failing when given path matches a redirect but response code is not equal to response code.
 * b) Failing when given path does not match but status code is not 301, 302, ...
 * c) Passes when given path does not match with a redirect.
 */
final class ResponseCode extends TesterBase
{
    protected function test(): TesterResult
    {
        $curlHandle = curl_init($this->testUrl);

        if ($curlHandle === false) {
            return new TesterResult(false, e(trans('vdlp.redirect::lang.test_lab.test_error')));
        }

        $this->setDefaultCurlOptions($curlHandle);

        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);

        $error = null;

        if (curl_exec($curlHandle) === false) {
            $error = curl_error($curlHandle);
        }

        if ($error !== null) {
            return new TesterResult(false, e(trans('vdlp.redirect::lang.test_lab.result_request_failed')));
        }

        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        curl_close($curlHandle);

        $manager = $this->getRedirectManager();

        try {
            $match = $manager->match(
                $this->testPath,
                $this->secure ? Redirect::SCHEME_HTTPS : Redirect::SCHEME_HTTP
            );
        } catch (NoMatchForRequest | InvalidScheme | UnableToLoadRules) {
            $match = false;
        }

        if ($match !== false && $match->getStatusCode() !== $statusCode) {
            $message = e(trans('vdlp.redirect::lang.test_lab.matched_not_http_code', [
                'expected' => $match->getStatusCode(),
                'received' => $statusCode,
            ]));

            return new TesterResult(false, $message);
        }

        if ($match !== false && $match->getStatusCode() === $statusCode) {
            $message = e(trans('vdlp.redirect::lang.test_lab.matched_http_code', [
                'code' => $statusCode,
            ]));

            return new TesterResult(true, $message);
        }

        // Should be a 301, 302, 303, 404, 410, ...
        if (!array_key_exists($statusCode, Redirect::$statusCodes)) {
            return new TesterResult(
                false,
                e(trans('vdlp.redirect::lang.test_lab.response_http_code_should_be'))
                . ' '
                . implode(', ', array_keys(Redirect::$statusCodes))
            );
        }

        return new TesterResult(
            true,
            e(trans('vdlp.redirect::lang.test_lab.response_http_code')) . ': ' . $statusCode
        );
    }
}
