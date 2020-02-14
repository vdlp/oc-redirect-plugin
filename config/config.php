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

    ]

];
