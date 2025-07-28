<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Backend\Models\BrandSetting;
use October\Rain\Support\Traits\Singleton;

final class BrandHelper
{
    use Singleton;

    private string $primaryColor;
    private string $secondaryColor;

    protected function init(): void
    {
        $brandSettings = new BrandSetting();

        // October CMS >3.3
        if (method_exists($brandSettings, 'getPaletteColors')) {
            $colorPalette = BrandSetting::get('color_palette');
            $colorMode = BrandSetting::getColorMode();

            $this->primaryColor = $colorPalette[$colorMode]['primary'] ?? '#6a6cf7';
            $this->secondaryColor = $colorPalette[$colorMode]['secondary'] ?? '#72809d';
        // October CMS <3.2
        } else {
            $this->primaryColor = $brandSettings->get('primary_color');
            $this->secondaryColor = $brandSettings->get('secondary_color');
        }
    }

    public function getPrimaryColor(): string
    {
        return $this->primaryColor;
    }

    public function getSecondaryColor(): string
    {
        return $this->secondaryColor;
    }

    public function getPrimaryOrSecondaryColor(bool $flag): string
    {
        return $flag ? $this->getPrimaryColor() : $this->getSecondaryColor();
    }
}
