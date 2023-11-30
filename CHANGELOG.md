# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.11] - 2023-11-30

* Add option to keep query string when redirecting.

## [3.1.10] - 2023-09-28

* Fixed backend filtering for MySQL >= 8.0.3.

## [3.1.9] - 2023-06-12

* Added environment variables for manipulating the navigation:
  * `VDLP_REDIRECT_SHOW_IMPORT`
  * `VDLP_REDIRECT_SHOW_EXPORT`
  * `VDLP_REDIRECT_SHOW_SETTINGS`
  * `VDLP_REDIRECT_SHOW_EXTENSION`
* UI optimizations

## [3.1.8] - 2023-05-25

* Add German translation (#104).

## [3.1.7] - 2023-04-19

* Remove use of old BrandSetting constants (#103).

## [3.1.6] - 2023-03-28

* Add support for October CMS 3.3
* UI optimizations

## [3.1.5] - 2023-03-20

* Prevent 'Uninitialized string offset 0' error.
* Updated Chart.js library to v4.2.1

## [3.1.4] - 2023-02-14

* Fix target field not loading properly.

## [3.1.3] - 2023-01-27

* Minor code improvements.

## [3.1.2] - 2022-12-09

* Fix daily stats labels when selecting month/year.

## [3.1.1] - 2022-06-24

* Add description column to Redirects overview.

## [3.1.0] - 2022-06-11

* Add support for October CMS 3.x.
* Drop support for October CMS 2.x.
* Minimum required PHP version is now 8.0.2

## [3.0.5] - 2022-06-11

* Lock to October CMS version 2.x. Support for October CMS 3 will be added in v3.1.0.

## [3.0.4] - 2022-04-19

* Improved compatibility/extensibility with other Plugins (Solves issue #90).

## [3.0.3] - 2022-03-15

* Update plugin dependencies.

## [3.0.2] - 2022-02-20

* Change version constraint for composer/installers.

## [3.0.1] - 2022-02-20

* Add support for regular expression matches in target path.

## [3.0.0] - 2022-02-19

* Drop support for October CMS 1.1 and lower.
* Minimum required PHP version is now 7.4
* Redirect to relative paths is now enabled by default.
* Add database relation to system request logs.
* When updating to `3.0.0` the table `vdlp_redirect_redirect_logs` will be removed due to schema changes.
* Improvements to redirect request logs.
* Improvements to redirect conditions logic.
* Improvements to the statistics page.
* Redirect Settings:
  * Relative paths setting is now **enabled** by default.
  * Testlab (Beta) is now **disabled** by default.
  * Redirect logging is now **disabled** by default.
  * Redirect statistics is now **disabled** by default.

## [2.6.0]

* Update plugin dependencies.

## [2.5.13]

* Fix database error when cache is being cleared before installation of plugin.

## [2.5.12]

* Fix strpos() type error.

## [2.5.11]

* Remove CMS support check.
* Fix bad use of import.

## [2.5.10]

* Add PHP 8.0 version constraint.
* Add composer/installers package.

## [2.5.9]

* Fix import in Plugin file.

## [2.5.8]

* Improve redirect caching management (revised).

## [2.5.7]

* Improve redirect caching management.

## [2.5.6]

* Prevent connection exception when accessing settings in CLI mode.

## [2.5.5]

* Suppress logging when redirect rules file is empty.

## [2.5.4]

* Add support for symfony/stopwatch:^5.0 (version 4.0 is still supported).
* Update Spanish language (thanks to Juan David M).
* Hide button "From Request log" when request logging is disabled.

## [2.5.3]

* Improve / fixes redirect rule caching (thanks to Eric Pfeiffer).
* Update Spanish language (thanks to Juan David M).
* Update Language files (help wanted!).

## [2.5.2]

* Fix bug that causes re-writing the redirect rules file when hits are updated.

## [2.5.1]

* Fixes issues with redirect rules file not being present.

## [2.5.0]

* Add support for using relative paths.

## [2.4.1]

* Add Redirect Extensions promo page.

## [2.4.0]

* Skip requests with header "X-Requested-With: XMLHttpRequest".

## [2.3.2]

* Improve error handling in plugin migration process.

## [2.3.1]

* Fix SQLSTATE[42S22] error when installing plugin.

## [2.3.0]

* Add new Redirect options:
    * Ignore Case
    * Ignore Trailing Slashes
* Fix date field timezone issue (scheduled tab).
* Fix warning dialog position (when scheduled redirect is not active).
* Minor translation improvements (en, nl).

## [2.2.0]

* Add "Cache-Control: no-store" header. This will prevent (modern) web browsers to cache the redirects. Very convenient when testing your redirects.
* Add extra tab "Event logs" to Redirect update page. This tab shows a list with the related event logs of the redirect.
* UI improvements.

## [2.1.1]

* Update CHANGELOG.

## [2.1.0]

* Improve exception handling #52.
* Add support for league/csv:9.0+.
* Improve caching mechanism #54.
* Suppress cache flush log message.
* Skip sparkline routes from being processed.

## [2.0.2]

* Force type of vdlp.redirect::log_redirect_changes #53.
* Apply config check to prevent log redirect changes #53.
* Convert database column types (char to varchar) #51.

## [2.0.1]

* Fix Middleware not being invoked in newer PHP versions.

## [2.0.0]

* Drop support for PHP 7.0, only supports PHP 7.1.3+.
* Most of the classes are made final. For extending use October CMS proposed solutions.
* Auto-redirect creation for CMS/Static pages has been removed from this plugin.
* The following events have been removed:
    * `vdlp.redirects.changed`
    * `vdlp.redirect.beforeRedirectSave`
    * `vdlp.redirect.beforeRedirectUpdate`
    * `vdlp.redirect.afterRedirectUpdate`
* New events:
    * `vdlp.redirect.changed`
    * `vdlp.redirect.changed`
