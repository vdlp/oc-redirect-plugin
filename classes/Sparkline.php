<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use InvalidArgumentException;

/**
 * Class Sparkline
 *
 * @package Vdlp\Redirect\Classes
 */
class Sparkline extends \Davaxi\Sparkline
{
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function setFillColorHex($color)
    {
        list($red, $green, $blue) = $this->colorHexToRGB($color);

        $baseRed = $baseGreen = $baseBlue = 255;

        $red = floor(($baseRed + $red) / 2);
        $green = floor(($baseGreen + $green) / 2);
        $blue = floor(($baseBlue + $blue) / 2);

        $this->setFillColorRGB($red, $green, $blue);
    }
}
