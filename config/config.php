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

        'publish-redirects' => env('VDLP_REDIRECT_CRON_PUBLISH_REDIRECTS', '00:00'),

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

    'log_redirect_changes' => env('VDLP_REDIRECT_LOG_REDIRECT_CHANGES', false),

];
