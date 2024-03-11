<?php

declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'Redirect',
        'description' => 'Easily manage redirects',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => 'Redirects',
            'tab' => 'Redirects',
        ],
    ],
    'navigation' => [
        'menu_label' => 'Redirects',
        'menu_description' => 'Manage redirects',
    ],
    'settings' => [
        'menu_label' => 'Redirects',
        'menu_description' => 'Manage settings for Redirects.',
        'logging_enabled_label' => 'Log redirect events',
        'logging_enabled_comment' => 'Store redirect events in the database.',
        'statistics_enabled_label' => 'Gather statistics',
        'statistics_enabled_comment' => 'Gather statistics of redirected requests to get more insight.',
        'test_lab_enabled_label' => 'TestLab (beta)',
        'test_lab_enabled_comment' => 'TestLab allows you to mass test your redirects.',
        'caching_enabled_label' => 'Caching of redirects (advanced)',
        'caching_enabled_comment' => 'Improves the redirect engine when having a lot of redirects. '
            . 'CAUTION: Cache driver `file` and `database` are NOT supported. '
            . 'Recommended driver is `memcached` or a similar "in-memory" caching driver.',
        'relative_paths_enabled_label' => 'Use relative paths',
        'relative_paths_enabled_command' => 'The redirect engine will generate relative paths instead of absolute paths.',
    ],
    'redirect' => [
        'redirect' => 'Redirect',
        'from_url' => 'Source Path',
        'from_url_placeholder' => '/source/path',
        'from_url_comment' => 'The source path to match.',
        'from_scheme' => 'Source scheme',
        'from_scheme_comment' => 'Force match on scheme. If HTTP is selected <u>http://domain.com/path</u> will '
            . 'match and <u>https://domain.com/path</u> does not match.',
        'to_url' => 'Target Path or URL',
        'to_url_placeholder' => '/absolute/path, relative/path or http://target.url',
        'to_url_comment' => 'The target path or URL to redirect to.',
        'to_url_required_if' => 'The target path or URL is required',
        'to_scheme' => 'Target scheme',
        'to_scheme_comment' => 'Target scheme will be forced to HTTP or HTTPS '
            . 'or choose AUTOMATIC to use the default scheme of the website.',
        'scheme_auto' => 'Automatic',
        'cms_page_required_if' => 'Please provide a CMS Page to redirect to',
        'static_page_required_if' => 'Please provide a Static Page to redirect to',
        'match_type' => 'Match Type',
        'exact' => 'Exact',
        'placeholders' => 'Placeholders',
        'regex' => 'Regular expression',
        'target_type' => 'Target Type',
        'target_type_none' => 'Not applicable',
        'target_type_path_or_url' => 'Path or URL',
        'target_type_cms_page' => 'CMS Page',
        'target_type_static_page' => 'Static Page',
        'status_code' => 'HTTP Status Code',
        'sort_order' => 'Sort Order',
        'requirements' => 'Requirements',
        'requirements_comment' => 'Provide requirements for each placeholder.',
        'placeholder' => 'Placeholder',
        'placeholder_comment' => 'The placeholder name (including curly braces) provided in the \'Source path\' field. '
            . 'E.g. {category} or {id}',
        'requirement' => 'Requirement',
        'requirement_comment' => 'Provide the requirement in regular expression syntax. E.g. [0-9]+ or [a-zA-Z]+.',
        'requirements_prompt' => 'Add new requirement',
        'replacement' => 'Replacement',
        'replacement_comment' => 'Provide an optional replacement value for this placeholder. '
            . 'The matched placeholder will be replaced with this value in the target URL.',
        'permanent' => '301 - Permanent',
        'temporary' => '302 - Temporary',
        'see_other' => '303 - See Other',
        'not_found' => '404 - Not Found',
        'gone' => '410 - Gone',
        'enabled' => 'Enabled',
        'none' => 'none',
        'enabled_comment' => 'Check this box to enable the redirect.',
        'priority' => 'Priority',
        'hits' => '# Hits',
        'return_to_redirects' => 'Return to redirects list',
        'return_to_categories' => 'Return to categories list',
        'delete_confirm' => 'Are you sure?',
        'created_at' => 'Created at',
        'modified_at' => 'Modified at',
        'system_tip' => 'System generated redirect',
        'user_tip' => 'User generated redirect',
        'type' => 'Type',
        'category' => 'Category',
        'categories' => 'Categories',
        'description' => 'Description',
        'name' => 'Name',
        'date_time' => 'Date & Time',
        'date' => 'Date',
        'truncate_confirm' => 'Are you sure you want to delete ALL records?',
        'truncating' => 'Deleting...',
        'warning' => 'Warning',
        'cache_warning' => 'You have enabled caching but your caching driver is not supported. '
            . 'Redirects will not be cached.',
        'general_confirm' => 'Are you sure you want to do this?',
        'sparkline_30d' => 'Hits (30d)',
        'has_hits' => 'Has hits',
        'minimum_hits' => 'Minimum # hits',
        'ignore_query_parameters' => 'Ignore query parameters',
        'ignore_query_parameters_comment' => 'The redirect engine will ignore all query parameters.',
        'ignore_case' => 'Ignore case',
        'ignore_case_comment' => 'The redirect engine will do case-insensitive matching.',
        'ignore_trailing_slash' => 'Ignore trailing slash',
        'ignore_trailing_slash_comment' => 'The redirect engine will ignore trailing slashes.',
        'keep_querystring' => 'Inherit querystring',
        'keep_querystring_comment' => 'Any query parameters present are passed to the target path or URL.',
        'last_used_at' => 'Last hit',
        'updated_at' => 'Updated at',
        'invalid_regex' => 'Invalid regular expression.',
        'created_due_to_bad_request' => 'Created due to bad a request.',
    ],
    'list' => [
        'no_records' => 'There are no redirects in this view.',
        'switch_is_enabled' => 'Enabled',
        'switch_system' => 'System Redirects',
    ],
    'scheduling' => [
        'from_date' => 'Available From',
        'from_date_comment' => 'The date on which this redirect will become active. Can be omitted.',
        'to_date' => 'Available Until',
        'to_date_comment' => 'The date until this redirect is active. Can be omitted.',
        'scheduling_comment' => 'Here you can provide a period on which this redirect will be available. '
            . 'All sorts of date combinations are possible.',
        'not_active_warning' => 'Redirect is not available anymore, please check \'Scheduling\' tab.',
    ],
    'test' => [
        'test_comment' => 'Please test your redirect before saving the redirect.',
        'input_path' => 'Input Path',
        'input_path_comment' => 'The input path to test. E.g. /old-blog/category/123',
        'input_path_placeholder' => '/input/path',
        'input_scheme' => 'Input scheme',
        'test_date' => 'Test Date',
        'test_date_comment' => 'If you scheduled this redirect, you can test this redirect on a certain date.',
        'testing' => 'Testing...',
        'run_test' => 'Run Test',
        'no_match_label' => 'Sorry, no match!',
        'no_match' => 'No match found!',
        'match_success_label' => 'We have a match!',
    ],
    'test_lab' => [
        'section_test_lab_comment' => 'TestLab allows you to mass test your redirects.',
        'test_lab_label' => 'Include in TestLab',
        'test_lab_enable' => 'Flick this switch to allow testing this redirect in the TestLab.',
        'test_lab_path_label' => 'Test Path',
        'test_lab_path_comment' => 'This path will be used when performing tests. '
            . 'Replace placeholders with real values.',
        'start_tests' => 'Start Tests',
        'start_tests_description' => 'Press the \'Start tests\' button to begin.',
        'edit' => 'Edit',
        'exclude' => 'Exclude',
        'exclude_confirm' => 'Are you sure want to exclude this redirect from TestLab?',
        'exclude_indicator' => 'Excluding redirect from TestLab',
        're_run' => 'Re-run',
        're_run_indicator' => 'Running tests, please wait...',
        'loop' => 'Loop',
        'match' => 'Match',
        'response_http_code' => 'Response HTTP code',
        'response_http_code_should_be' => 'Response HTTP code should be one of:',
        'redirect_count' => 'Redirect count',
        'final_destination' => 'Final Destination',
        'no_redirects' => 'No redirects have been marked with TestLab enabled. '
            . 'Please enable the option \'Include in TestLab\' when editing a redirect.',
        'test_error' => 'An error occurred when testing this redirect.',
        'flash_test_executed' => 'Test has been executed.',
        'flash_redirect_excluded' => 'Redirect has been excluded from TestLab and will not show up on next test run.',
        'result_request_failed' => 'Could not execute request.',
        'redirects_followed' => 'Number of redirects followed: :count (limited to :limit)',
        'not_determinate_destination_url' => 'Could not determine final destination URL.',
        'no_destination_url' => 'No final destination URL.',
        'final_destination_is' => 'Final destination is: :destination',
        'possible_loop' => 'Possible redirect loop!',
        'no_loop' => 'No redirect loop detected.',
        'not_match_redirect' => 'Did not match any redirect.',
        'matched' => 'Matched',
        'redirect' => 'redirect',
        'matched_not_http_code' => 'Matched redirect, but response HTTP code did not match! '
            . 'Expected :expected but received :received.',
        'matched_http_code' => 'Matched redirect, response HTTP code :code.',
        'executing_tests' => 'Executing tests...',
    ],
    'statistics' => [
        'hits_per_day' => 'Redirect hits per day',
        'click_on_chart' => 'Click on the chart to enable zooming and dragging.',
        'requests_redirected' => 'Requests redirected',
        'all_time' => 'all time',
        'active_redirects' => 'Active redirects',
        'redirects_this_month' => 'Redirects this month',
        'previous_month' => 'previous month',
        'latest_redirected_requests' => 'Latest redirected request',
        'redirects_per_month' => 'Redirects per month',
        'no_data' => 'No data yet',
        'top_crawlers_this_month' => 'Top :top crawlers this month',
        'top_redirects_this_month' => 'Top :top redirects this month',
        'activity_last_three_months' => 'Activity last 3 months',
        'crawler_hits' => 'Crawler hits',
        'visitor_hits' => 'Visitor hits',
    ],
    'title' => [
        'import' => 'Import',
        'export' => 'Export',
        'redirects' => 'Manage redirects',
        'create_redirect' => 'Create redirect',
        'edit_redirect' => 'Edit redirect',
        'categories' => 'Manage categories',
        'create_category' => 'Create category',
        'edit_category' => 'Edit category',
        'view_redirect_log' => 'Event log',
        'statistics' => 'Statistics',
        'test_lab' => 'TestLab (beta)',
    ],
    'buttons' => [
        'add' => 'Add',
        'from_request_log' => 'From Request log',
        'new_redirect' => 'New redirect',
        'create_redirects' => 'Create redirects',
        'create_redirect' => 'Create redirect',
        'create_and_new' => 'Create and new',
        'delete' => 'Delete',
        'enable' => 'Enable',
        'disable' => 'Disable',
        'reorder_redirects' => 'Reorder',
        'export' => 'Export',
        'import' => 'Import',
        'settings' => 'Settings',
        'categories' => 'Categories',
        'extensions' => 'Extensions',
        'new_category' => 'New category',
        'reset_statistics' => 'Reset statistics',
        'logs' => 'Event log',
        'empty_redirect_log' => 'Empty event log',
        'clear_cache' => 'Clear cache',
        'stop' => 'Stop',
        'reset_all' => 'Reset statistics for all redirects',
        'all_redirects' => 'all redirects',
        'bulk_actions' => 'Bulk actions',
    ],
    'tab' => [
        'tab_general' => 'General',
        'tab_requirements' => 'Requirements',
        'tab_test' => 'Test',
        'tab_scheduling' => 'Scheduling',
        'tab_test_lab' => 'TestLab',
        'tab_advanced' => 'Advanced',
        'tab_logs' => 'Event log',
    ],
    'flash' => [
        'success_created_redirects' => 'Successfully created :count redirects',
        'static_page_redirect_not_supported' => 'This redirect cannot be modified. Plugin RainLab.Pages is required.',
        'truncate_success' => 'Successfully deleted all records',
        'delete_selected_success' => 'Successfully deleted selected records',
        'cache_cleared_success' => 'Successfully cleared redirect cache',
        'statistics_reset_success' => 'All statistics have been successfully reset',
        'enabled_all_redirects_success' => 'All redirects have been successfully enabled',
        'disabled_all_redirects_success' => 'All redirects have been successfully disabled',
        'deleted_all_redirects_success' => 'All redirects have been successfully deleted',
    ],
    'import_export' => [
        'match_type' => 'Match Type [match_type] (Allowed values: exact, placeholders, regex)',
        'category_id' => 'Category [category_id]',
        'target_type' => 'Target Type [target_type] (Allowed values: path_or_url, cms_page, static_page, none)',
        'from_url' => 'Source path [from_url]',
        'from_scheme' => 'Source scheme [from_scheme] (Allowed values: http, https, auto [default])',
        'to_url' => 'Target path [to_url]',
        'to_scheme' => 'Target scheme [to_scheme] (Allowed values: http, https, auto [default])',
        'test_url' => 'Test URL [test_url]',
        'cms_page' => 'CMS Page [cms_page] (Filename without .htm extension)',
        'static_page' => 'Static Page [static_page] (Filename without .htm extension)',
        'requirements' => 'Placeholder requirements [requirements]',
        'status_code' => 'HTTP status code [status_code] (Possible values: 301, 302, 303, 404, 410)',
        'hits' => 'Redirect Hits [hits]',
        'from_date' => 'Scheduled date from [from_date] (YYYY-MM-DD or empty)',
        'to_date' => 'Scheduled date to [to_date] (YYYY-MM-DD or empty)',
        'sort_order' => 'Priority [sort_order]',
        'is_enabled' => 'Enabled [is_enabled] (1 = enable redirect, 0 = disable redirect [default])',
        'ignore_query_parameters' => 'Ignore Query Parameters [ignore_query_parameters] (1 = yes, 0 = no [default])',
        'ignore_case' => 'Ignore Case [ignore_case] (1 = yes, 0 = no [default])',
        'ignore_trailing_slash' => 'Ignore Trailing Slashes [ignore_trailing_slash] (1 = yes, 0 = no [default])',
        'test_lab' => 'Test Lab [test_lab] (1 = enable Test Lab, 0 = disable TestLab [default])',
        'test_lab_path' => 'Test Lab path [test_lab_path] (required if match_type = placeholders)',
        'system' => 'System [system] (1 = system generated redirect, 0 = user generated redirect [default])',
        'description' => 'Description [description]',
        'last_used_at' => 'Last Used At [last_used_at] (YYYY-MM-DD HH:MM:SS or empty)',
        'created_at' => 'Created At [created_at] (YYYY-MM-DD HH:MM:SS or empty)',
        'updated_at' => 'Updated At [updated_at] (YYYY-MM-DD HH:MM:SS or empty)',
    ],
    'html_lang' => [
        'status_code_comment' =>'<label>HTTP Status Code <span class="status-code-info icon-question-circle"data-control="popup" data-handler="onShowStatusCodeInfo" data-keyboard="true" data-size="huge"></span></label>',
        'status_code_info' => '<h3>301 (Moved Permanently)</h3>
        <p>The 301 (Moved Permanently) status code indicates that the target
            resource has been assigned a new permanent URI and any future
            references to this resource ought to use one of the enclosed URIs.
            Clients with link-editing capabilities ought to automatically re-link
            references to the effective request URI to one or more of the new
            references sent by the server, where possible.</p>
    
        <h3>302 (Found)</h3>
        <p>The 302 (Found) status code indicates that the target resource
            resides temporarily under a different URI. Since the redirection
            might be altered on occasion, the client ought to continue to use the
            effective request URI for future requests.
        </p>
    
        <h3>303 (See Other)</h3>
        <p>The 303 (See Other) status code indicates that the server is
            redirecting the user agent to a different resource, as indicated by a
            URI in the Location header field, which is intended to provide an
            indirect response to the original request. A user agent can perform
            a retrieval request targeting that URI (a GET or HEAD request if
            using HTTP), which might also be redirected, and present the eventual
            result as an answer to the original request. Note that the new URI
            in the Location header field is not considered equivalent to the
            effective request URI.</p>
    
        <h3>404 (Not Found)</h3>
        <p>The 404 (Not Found) status code indicates that the origin server did
            not find a current representation for the target resource or is not
            willing to disclose that one exists. A 404 status code does not
            indicate whether this lack of representation is temporary or
            permanent; the 410 (Gone) status code is preferred over 404 if the
            origin server knows, presumably through some configurable means, that
            the condition is likely to be permanent.</p>
    
        <h3>410 (Gone)</h3>
        <p>The 410 (Gone) status code indicates that access to the target
            resource is no longer available at the origin server and that this
            condition is likely to be permanent. If the origin server does not
            know, or has no facility to determine, whether or not the condition
            is permanent, the status code 404 (Not Found) ought to be used
            instead.</p>',
    ],
];
