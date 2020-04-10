# 2.2.0

* Add "Cache-Control: no-store" header. This will prevent (modern) web browsers to cache the redirects. Very convenient when testing your redirects.
* Add extra tab "Event logs" to Redirect update page. This tab shows a list with the related event logs of the redirect.
* UI improvements

# 2.1.1

* Update CHANGELOG

# 2.1.0

* Improve exception handling #52
* Add support for league/csv:9.0+
* Improve caching mechanism #54
* Suppress cache flush log message
* Skip sparkline routes from being processed

# 2.0.2

* Force type of vdlp.redirect::log_redirect_changes #53
* Apply config check to prevent log redirect changes #53
* Convert database column types (char to varchar) #51

# 2.0.1

* Fix Middleware not being invoked in newer PHP versions

# 2.0.0

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
