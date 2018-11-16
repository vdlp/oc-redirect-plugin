<?php

declare(strict_types=1);

use Vdlp\Redirect\Classes\Sparkline;
use Vdlp\Redirect\Classes\StatisticsHelper;
use Backend\Models\BrandSetting;

Route::group(['middleware' => ['web']], function () {
    Route::get('vdlp/redirect/sparkline/{redirectId}', function ($redirectId) {
        if (!BackendAuth::check()) {
            return response('Forbidden', 403);
        }

        /** @var \Illuminate\Http\Request $request */
        $request = resolve(\Illuminate\Http\Request::class);

        $crawler = $request->has('crawler');

        $cacheKey = sprintf('vdlp_redirect_%d_%d', (int) $redirectId, (int) $crawler);

        $data = Cache::remember($cacheKey, 5, function () use ($redirectId, $crawler) {
            return (new StatisticsHelper())->getRedirectHitsSparkline((int) $redirectId, $crawler);
        });

        $imageData = Cache::remember($cacheKey . '_image', 5, function () use ($crawler, $data) {
            $primaryColor = BrandSetting::get(
                $crawler ? 'primary_color' : 'secondary_color',
                $crawler ? BrandSetting::PRIMARY_COLOR : BrandSetting::SECONDARY_COLOR
            );

            $sparkline = new Sparkline();
            $sparkline->setFormat('200x60');
            $sparkline->setPadding('2 0 0 2');
            $sparkline->setData($data);
            $sparkline->setLineThickness(3);
            $sparkline->setLineColorHex($primaryColor);
            $sparkline->setFillColorHex($primaryColor);
            $sparkline->deactivateBackgroundColor();

            return $sparkline->toBase64();
        });

        // TODO: Leverage Browser Caching

        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="' . $cacheKey . '.png"');
        header('Accept-Ranges: none');

        echo base64_decode($imageData);

        exit();
    });
});
