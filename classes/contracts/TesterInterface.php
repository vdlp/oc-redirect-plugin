<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes\Contracts;

use Vdlp\Redirect\Classes\TesterResult;

interface TesterInterface
{
    /**
     * Execute the test
     *
     * @return TesterResult
     */
    public function execute(): TesterResult;

    /**
     * The testers' test path. E.g /test/path
     *
     * @return string
     */
    public function getTestPath(): string;

    /**
     * The testers' full test URL. E.g. https://test.com/test/path
     *
     * @return string
     */
    public function getTestUrl(): string;
}
