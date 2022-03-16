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
    public function setFillColorHex(string $color, int $seriesIndex = 0): void
    {
        [$red, $green, $blue] = $this->colorHexToRGB($color);

        $baseRed = $baseGreen = $baseBlue = 255;

        $red = (int) floor(($baseRed + $red) / 2);
        $green = (int) floor(($baseGreen + $green) / 2);
        $blue = (int) floor(($baseBlue + $blue) / 2);

        $this->setFillColorRGB($red, $green, $blue);
    }
}
