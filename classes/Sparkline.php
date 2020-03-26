<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use InvalidArgumentException;

final class Sparkline extends \Davaxi\Sparkline
{
    /**
     * @throws InvalidArgumentException
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function setFillColorHex($color, $seriesIndex = 0): void
    {
        [$red, $green, $blue] = $this->colorHexToRGB($color);

        $baseRed = $baseGreen = $baseBlue = 255;

        $red = floor(($baseRed + $red) / 2);
        $green = floor(($baseGreen + $green) / 2);
        $blue = floor(($baseBlue + $blue) / 2);

        $this->setFillColorRGB($red, $green, $blue);
    }
}
