<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Daily cron schedule configuration
    |--------------------------------------------------------------------------
    |
    | Schedule the time for each cron task. Accepted format: "HH:MM".
    |
    */

    'cron' => [

        'publish_redirects' => env('VDLP_REDIRECT_CRON_PUBLISH_REDIRECTS', '00:00'),

    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific logging information. Commonly used for
    | debugging purposes.
    |
    */

    'log_redirect_changes' => (bool) env('VDLP_REDIRECT_LOG_REDIRECT_CHANGES', false),

    /*
    |--------------------------------------------------------------------------
    | Redirect Rules Path
    |--------------------------------------------------------------------------
    |
    | The path of the redirect rules. Make sure the path is writable.
    |
    */

    'rules_path' => env('VDLP_REDIRECT_RULES_PATH', storage_path('app/redirects.csv')),

];
