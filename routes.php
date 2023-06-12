<?php

declare(strict_types=1);

use Backend\Facades\BackendAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Vdlp\Redirect\Classes\BrandHelper;
use Vdlp\Redirect\Classes\Sparkline;
use Vdlp\Redirect\Classes\StatisticsHelper;

Route::group(['middleware' => ['web']], static function (): void {
    Route::get('vdlp/redirect/sparkline/{redirectId}', static function (Request $request, $redirectId) {
        if (!BackendAuth::check()) {
            return response('Forbidden', 403);
        }

        $crawler = $request->has('crawler');

        $preset = $request->get('preset', '30d-small');

        $properties = [
            'format' => '200x60',
            'lineThickness' => 3,
            'days' => 30,
        ];

        if ($preset === '3m-large') {
            $properties = [
                'format' => '520x120',
                'lineThickness' => 2,
                'days' => 90,
            ];
        }

        $cacheKey = sprintf('vdlp_redirect_%s_%d_%d', $preset, (int) $redirectId, (int) $crawler);

        $data = Cache::remember($cacheKey, 5 * 60, static function () use ($redirectId, $crawler, $properties) {
            return (new StatisticsHelper())->getRedirectHitsSparkline((int) $redirectId, $crawler, $properties['days']);
        });

        // TODO: Generate fallback image data if generating image fails.
        $imageData = Cache::remember($cacheKey . '_image', 5 * 60, static function () use ($crawler, $data, $properties) {
            $primaryColor = BrandHelper::instance()
                ->getPrimaryOrSecondaryColor($crawler);

            $sparkline = new Sparkline();
            $sparkline->setFormat($properties['format']);
            $sparkline->setPadding('2 0 0 2');
            $sparkline->setData($data);
            $sparkline->setLineThickness($properties['lineThickness']);
            $sparkline->setLineColorHex($primaryColor);
            $sparkline->setFillColorHex($primaryColor);
            $sparkline->deactivateBackgroundColor();

            return $sparkline->toBase64();
        });

        // TODO: Leverage Browser Caching
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="' . $cacheKey . '.png"');
        header('Accept-Ranges: none');

        echo base64_decode($imageData, true);

        exit(0);
    });
});
