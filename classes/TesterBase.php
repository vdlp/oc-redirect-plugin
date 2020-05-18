<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Cms;
use InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use Vdlp\Redirect\Classes\Contracts\RedirectManagerInterface;
use Vdlp\Redirect\Classes\Contracts\TesterInterface;
use Vdlp\Redirect\Models\Settings;

abstract class TesterBase implements TesterInterface
{
    /**
     * Maximum redirects to follow.
     *
     * @var int
     */
    public const MAX_REDIRECTS = 10;

    /**
     * Connection timeout in seconds.
     *
     * @var int
     */
    public const CONNECTION_TIMEOUT = 10;

    /**
     * @var string
     */
    protected $testUrl;

    /**
     * @var string
     */
    protected $testPath;

    public function __construct(string $testPath)
    {
        $this->testPath = $testPath;
        $this->testUrl = Cms::url($testPath);
    }

    final public function execute(): TesterResult
    {
        $stopwatch = new Stopwatch();

        $stopwatch->start(__FUNCTION__);

        $result = $this->test();

        $event = $stopwatch->stop(__FUNCTION__);

        $result->setDuration((int) $event->getDuration());

        return $result;
    }

    public function getTestPath(): string
    {
        return $this->testPath;
    }

    public function getTestUrl(): string
    {
        return $this->testUrl;
    }

    abstract protected function test(): TesterResult;

    /**
     * @throws InvalidArgumentException
     */
    protected function setDefaultCurlOptions($curlHandle): void
    {
        if (!is_resource($curlHandle)) {
            throw new InvalidArgumentException('Argument must be a valid resource type.');
        }

        curl_setopt($curlHandle, CURLOPT_MAXREDIRS, self::MAX_REDIRECTS);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, self::CONNECTION_TIMEOUT);
        curl_setopt($curlHandle, CURLOPT_AUTOREFERER, true);

        // This constant is not available when open_basedir or safe_mode are enabled.
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);

        /** @noinspection CurlSslServerSpoofingInspection */
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
        /** @noinspection CurlSslServerSpoofingInspection */
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        if (defined('CURLOPT_SSL_VERIFYSTATUS')) {
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYSTATUS, false);
        }

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_VERBOSE, false);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, [
            'X-Vdlp-Redirect: Tester'
        ]);
    }

    /**
     * @return RedirectManagerInterface
     */
    protected function getRedirectManager(): RedirectManagerInterface
    {
        /** @var RedirectManagerInterface $manager */
        $manager = resolve(RedirectManagerInterface::class);
        return $manager->setSettings(new RedirectManagerSettings(
            false,
            false,
            Settings::isRelativePathsEnabled()
        ));
    }
}
