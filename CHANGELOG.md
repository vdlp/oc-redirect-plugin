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
