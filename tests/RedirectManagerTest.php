<?php

/** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Vdlp\Redirect\Tests;

use Carbon\Carbon;
use Cms;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Exception;
use PluginTestCase;
use Vdlp\Redirect\Classes\Exceptions\InvalidScheme;
use Vdlp\Redirect\Classes\Exceptions\NoMatchForRequest;
use Vdlp\Redirect\Classes\RedirectManager;
use Vdlp\Redirect\Classes\RedirectRule;
use Vdlp\Redirect\Models\Redirect;
use Vdlp\Redirect\ServiceProvider;

class RedirectManagerTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->register(ServiceProvider::class);
    }

    /**
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     */
    public function testExactRedirect(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'requirements' => null,
            'status_code' => 302,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $test = '/this-should-be-source';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));

        $test = '/this-is-something-totally-different';

        $this->expectException(NoMatchForRequest::class);

        $manager->match($test, Redirect::SCHEME_HTTPS);
    }

    /**
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     */
    public function testRedirectWithIgnoreQueryParametersEnabled(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'ignore_query_parameters' => true,
            'requirements' => null,
            'status_code' => 302,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $test = '/this-should-be-source?foo=bar';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));

        $test = '/this-should-be-source';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));
    }

    /**
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function testRedirectWithIgnoreCaseEnabled(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-SHOULD-be-source',
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'ignore_case' => true,
            'requirements' => null,
            'status_code' => 302,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $test = '/ThIs-sHoUlD-bE-sOuRce';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));

        $test = '/this-SHOULD-be-source';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));
    }

    /**
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     */
    public function testRedirectWithIgnoreCaseEnabledAndQueryParameters(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-SHOULD-be-source?PARAM1=lower&param2=HIGHER',
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'ignore_query_parameters' => false,
            'ignore_case' => true,
            'requirements' => null,
            'status_code' => 302,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $test = '/ThIs-sHoUlD-bE-sOuRce?param1=LOWER&param2=HIGHER';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));

        $test = '/this-should-be-SOURCE?param1=lower&param2=higher';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));
    }

    /**
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function testRedirectWithIgnoreTrailingSlashEnabled(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'ignore_trailing_slash' => true,
            'requirements' => null,
            'status_code' => 302,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $test = '/this-should-be-source//';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));
    }

    /**
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function testRedirectWithIgnoreTrailingSlashEnabledAndQueryParameters(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source?param1=value&param2=value',
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'ignore_query_parameters' => false,
            'ignore_trailing_slash' => true,
            'requirements' => null,
            'status_code' => 302,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $test = '/this-should-be-source/?param1=value&param2=value';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));

        $test = '/this-should-be-source//?param1=value&param2=value';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));

        $test = '/this-should-be-source//////////////?param1=value&param2=value';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));
    }

    /**
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     */
    public function testRedirectWithIgnoreQueryParametersDisabled(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_url' => '/this-should-be-source',
            'from_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'ignore_query_parameters' => false,
            'requirements' => null,
            'status_code' => 302,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $test = '/this-should-be-source?foo=bar';

        try {
            $result = $manager->match($test, Redirect::SCHEME_HTTPS);
        } catch (NoMatchForRequest $e) {
            $result = false;
        }

        self::assertFalse($result);

        $test = '/this-should-be-source';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertInstanceOf(RedirectRule::class, $result);
        self::assertEquals(Cms::url('/this-should-be-target'), $manager->getLocation($result));
    }

    /**
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     */
    public function testPlaceholderRedirect(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_PLACEHOLDERS,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/blog.php?cat={category}&section={section}&id={id}',
            'to_url' => '/blog/{category}/{section}/{id}',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'requirements' => [
                [
                    'placeholder' => '{category}',
                    'requirement' => '(octobercms|wordpress|drupal)',
                    'replacement' => null,
                ],
                [
                    'placeholder' => '{section}',
                    'requirement' => '[a-z]+',
                    'replacement' => null,
                ],
                [
                    'placeholder' => '{id}',
                    'requirement' => '[0-9]{2}',
                    'replacement' => null,
                ],
            ],
            'status_code' => 301,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $test = '/blog.php?cat=octobercms&section=test&id=1337';

        try {
            $result = $manager->match($test, Redirect::SCHEME_HTTPS);
        } catch (NoMatchForRequest $e) {
            $result = false;
        }

        self::assertFalse($result);

        $test = '/blog.php?cat=octobercms&section=test&id=13';
        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertInstanceOf(RedirectRule::class, $result);
        self::assertEquals(Cms::url('/blog/octobercms/test/13'), $manager->getLocation($result));

        $test = '/blog.php?cat=wordpress&section=test&id=99';

        $result = $manager->match($test, Redirect::SCHEME_HTTPS);

        self::assertInstanceOf(RedirectRule::class, $result);
        self::assertEquals(Cms::url('/blog/wordpress/test/99'), $manager->getLocation($result));

        $test = '/blog.php?cat=joomla&section=test&id=99';

        try {
            $result = $manager->match($test, Redirect::SCHEME_HTTPS);
        } catch (NoMatchForRequest $e) {
            $result = false;
        }

        self::assertFalse($result);

        $test = '/blog.php?cat=drupal&section=test&id=e9';

        try {
            $result = $manager->match($test, Redirect::SCHEME_HTTPS);
        } catch (NoMatchForRequest $e) {
            $result = false;
        }

        self::assertFalse($result);
    }

    /**
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws Exception
     */
    public function testTargetCmsPageRedirect(): void
    {
        $page = Page::load(Theme::getActiveTheme(), 'vdlp-redirect-testpage');

        if ($page === null) {
            $page = new Page();
            $page->title = 'Testpage';
            $page->url = '/vdlp/redirect/testpage';
            $page->setFileNameAttribute('vdlp-redirect-testpage');
            $page->save();
        }

        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_CMS_PAGE,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/this-should-be-source',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'cms_page' => 'vdlp-redirect-testpage',
            'requirements' => null,
            'status_code' => 302,
            'from_date' => Carbon::today(),
            'to_date' => Carbon::today()->addWeek(),
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $result = $manager->match('/this-should-be-source', Redirect::SCHEME_HTTPS);

        self::assertInstanceOf(RedirectRule::class, $result);
        self::assertEquals(Cms::url('/vdlp/redirect/testpage'), $manager->getLocation($result));

        self::assertTrue($page->delete());
    }

    /**
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     */
    public function testScheduledRedirectPeriod(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/this-should-be-source',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'requirements' => null,
            'status_code' => 302,
            'from_date' => Carbon::today(),
            'to_date' => Carbon::today()->addWeek(),
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        // Test date between `from_date` and `end_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addDay(2))
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS)
        );

        // Test date equals `from_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS)
        );

        // Test date equals `to_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addWeek())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS)
        );

        // Test date greater than `to_date`
        try {
            $result = $manager->setMatchDate(Carbon::today()->addWeek()->addDay())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS);
        } catch (NoMatchForRequest $e) {
            $result = false;
        }

        self::assertFalse($result);

        // Test date less than `from_date`
        try {
            $result = $manager->setMatchDate(Carbon::today()->subDay())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS);
        } catch (NoMatchForRequest $e) {
            $result = false;
        }

        self::assertFalse($result);
    }

    /**
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidScheme
     */
    public function testScheduledRedirectOnlyFromDate(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/this-should-be-source',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'requirements' => null,
            'status_code' => 302,
            'is_enabled' => 1,
            'from_date' => Carbon::today()->addMonth(),
            'to_date' => null,
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        // Test date equals `from_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addMonth())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS)
        );

        // Test date greater than `from_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addMonth()->addDay())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS)
        );

        // Test date less than `from_date`
        try {
            $result = $manager->setMatchDate(Carbon::today()->addMonth()->subDay())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS);
        } catch (NoMatchForRequest $e) {
            $result = false;
        }

        self::assertFalse($result);
    }

    /**
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidScheme
     */
    public function testScheduledRedirectOnlyToDate(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/this-should-be-source',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'requirements' => null,
            'status_code' => 302,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => Carbon::today()->addMonth(),
        ]);

        self::assertTrue($redirect->save());

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        // Test date equals `to_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addMonth())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS)
        );

        // Test date less than `to_date`
        self::assertInstanceOf(
            RedirectRule::class,
            $manager->setMatchDate(Carbon::today()->addMonth()->subDay())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS)
        );

        // Test date greater than `to_date`
        try {
            $result = $manager->setMatchDate(Carbon::today()->addMonth()->addDay())
                ->match('/this-should-be-source', Redirect::SCHEME_HTTPS);
        } catch (NoMatchForRequest $e) {
            $result = false;
        }

        self::assertFalse($result);
    }

    public function testRelativeRedirect(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/this-should-be-source',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => 'relative/path/to',
            'requirements' => null,
            'status_code' => 302,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => null,
        ]);

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        self::assertEquals(Cms::url('/relative/path/to'), $manager->getLocation($rule));

        $manager->setBasePath('/subdirectory');

        self::assertEquals(Cms::url('/subdirectory/relative/path/to'), $manager->getLocation($rule));

        $manager->setBasePath('/subdirectory/sub/sub//');

        self::assertEquals('/subdirectory/sub/sub', $manager->getBasePath());
        self::assertEquals(Cms::url('/subdirectory/sub/sub/relative/path/to'), $manager->getLocation($rule));
    }

    /**
     * @throws Cms\Classes\CmsException
     */
    public function testAbsoluteRedirect(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/this-should-be-source',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/absolute/path/to',
            'requirements' => null,
            'status_code' => 302,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => null,
        ]);

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        self::assertEquals(Cms::url('/absolute/path/to'), $manager->getLocation($rule));

        $manager->setBasePath('/subdirectory');

        self::assertEquals(Cms::url('/absolute/path/to'), $manager->getLocation($rule));

        $manager->setBasePath('/subdirectory/sub/sub');

        self::assertEquals(Cms::url('/absolute/path/to'), $manager->getLocation($rule));
    }

    /**
     * @throws InvalidScheme
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function testSchemeHttpToHttpsRedirect(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_HTTP,
            'from_url' => '/this-should-be-http-source',
            'to_scheme' => Redirect::SCHEME_HTTPS,
            'to_url' => '/this-should-be-https-target',
            'requirements' => null,
            'status_code' => 302,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => null,
        ]);

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        // This one should not match
        try {
            $rule = $manager->match('/this-should-be-http-source', Redirect::SCHEME_HTTPS);
        } catch (NoMatchForRequest $e) {
            $rule = false;
        }

        self::assertFalse($rule);

        // This one should match
        try {
            $rule = $manager->match('/this-should-be-http-source', Redirect::SCHEME_HTTP);
        } catch (NoMatchForRequest $e) {
            $rule = false;
        }

        self::assertNotFalse($rule);

        // Cms::url() returns http://localhost by default.
        $expectedTargetUrl = Cms::url('/this-should-be-https-target');

        // Now construct the expected target URL.
        $expectedTargetUrl = str_replace('http://', 'https://', $expectedTargetUrl);

        // Get the actual target URL
        $actualTargetUrl = $manager->getLocation($rule);

        // These should be equal
        self::assertEquals($expectedTargetUrl, $actualTargetUrl);
    }

    /**
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws PHPUnit_Framework_Exception
     */
    public function testSchemeHttpsToHttpRedirect(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_HTTPS,
            'from_url' => '/this-should-be-https-source',
            'to_scheme' => Redirect::SCHEME_HTTP,
            'to_url' => '/this-should-be-http-target',
            'requirements' => null,
            'status_code' => 301,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => null,
        ]);

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        // This one should not match
        try {
            $rule = $manager->match('/this-should-be-https-source', Redirect::SCHEME_HTTP);
        } catch (NoMatchForRequest $e) {
            $rule = false;
        }

        self::assertFalse($rule);

        // This one should match
        $rule = $manager->match('/this-should-be-https-source', Redirect::SCHEME_HTTPS);

        self::assertInstanceOf(RedirectRule::class, $rule);

        // Cms::url() returns http://localhost by default.
        $expectedTargetUrl = Cms::url('/this-should-be-http-target');
        $expectedTargetUrl = str_replace('https', 'http', $expectedTargetUrl);

        // Get the actual target URL
        $actualTargetUrl = $manager->getLocation($rule);

        // These should be equal
        self::assertEquals($expectedTargetUrl, $actualTargetUrl);
    }

    /**
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws PHPUnit_Framework_Exception
     */
    public function testSchemeAutoToAutoRedirect(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/this-should-be-auto-source',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-auto-target',
            'requirements' => null,
            'status_code' => 301,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => null,
        ]);

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        // This one should match
        $rule = $manager->match('/this-should-be-auto-source', Redirect::SCHEME_HTTP);

        self::assertInstanceOf(RedirectRule::class, $rule);

        // This one should also match
        $rule = $manager->match('/this-should-be-auto-source', Redirect::SCHEME_HTTPS);

        self::assertInstanceOf(RedirectRule::class, $rule);

        // Cms::url() returns http://localhost by default.
        $expectedTargetUrl = Cms::url('/this-should-be-auto-target');

        // Get the actual target URL
        $actualTargetUrl = $manager->getLocation($rule);

        // These should be equal
        self::assertEquals($expectedTargetUrl, $actualTargetUrl);
    }

    /**
     * @throws InvalidScheme
     * @throws NoMatchForRequest
     * @throws PHPUnit_Framework_Exception
     */
    public function testRegularExpressionRedirect(): void
    {
        $redirect = new Redirect([
            'match_type' => Redirect::TYPE_REGEX,
            'target_type' => Redirect::TARGET_TYPE_PATH_URL,
            'from_scheme' => Redirect::SCHEME_AUTO,
            'from_url' => '/\/file\.(css|js)/',
            'to_scheme' => Redirect::SCHEME_AUTO,
            'to_url' => '/this-should-be-target',
            'requirements' => null,
            'status_code' => 301,
            'is_enabled' => 1,
            'from_date' => null,
            'to_date' => null,
        ]);

        $rule = RedirectRule::createWithModel($redirect);
        $manager = RedirectManager::createWithRule($rule);

        $rule = $manager->match('/file.css', Redirect::SCHEME_HTTP);

        self::assertInstanceOf(RedirectRule::class, $rule);

        $rule = $manager->match('/file.js', Redirect::SCHEME_HTTP);

        self::assertInstanceOf(RedirectRule::class, $rule);
    }
}
