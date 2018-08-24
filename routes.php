<?php

declare(strict_types=1);

use Vdlp\Redirect\Classes\Sparkline;
use Vdlp\Redirect\Classes\StatisticsHelper;
use Backend\Models\BrandSetting;

Route::group(['middleware' => ['web']], function () {
    Route::get('vdlp/redirect/sparkline/{redirectId}', function ($redirectId) {
        if (!BackendAuth::check()) {
            return Redirect::home();
        }

        $primaryColor = BrandSetting::get('primary_color', BrandSetting::PRIMARY_COLOR);

        $sparkline = new Sparkline();
        $sparkline->setFormat('200x60');
        $sparkline->setPadding('2 0 0 2');
        $sparkline->setExpire('+5 minutes');
        $sparkline->setData((new StatisticsHelper())->getRedirectHitsSparkline((int) $redirectId));
        $sparkline->setLineThickness(3.5);
        $sparkline->setLineColorHex($primaryColor);
        $sparkline->setFillColorHex($primaryColor);
        $sparkline->deactivateBackgroundColor();
        $sparkline->display();
    });
});
