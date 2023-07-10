<?php declare(strict_types=1);

/**
 * This file is part of the Nette Palette (https://github.com/MichaelPavlista/nette-palette)
 * Copyright (c) 2016 Michael Pavlista (http://www.pavlista.cz/)
 *
 * @author Michael Pavlista
 * @email  michael@pavlista.cz
 * @link   http://pavlista.cz/
 * @link   https://www.facebook.com/MichaelPavlista
 * @copyright 2016
 */

namespace App\Model\Extensions\NettePalette\Latte;

use App\Model\Extensions\NettePalette\Palette;
use Palette\Exception;

/**
 * Providing Palette support to Latte template engine
 * Class LatteFilter
 * @package NettePalette\Latte
 */
final class LatteFilter
{
    /** @var Palette service */
    private Palette $palette;


    /**
     * LatteFilter constructor.
     * @param Palette $palette
     */
    public function __construct(Palette $palette)
    {
        $this->palette = $palette;
    }


    /**
     * Return url to required image
     * @param string $image
     * @param string|null $imageQuery
     * @return string|null
     * @throws Exception
     */
    public function __invoke(string $image, ?string $imageQuery = NULL): ?string
    {
        return $this->palette->getUrl($image, $imageQuery);
    }
}
