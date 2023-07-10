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

namespace App\Model\Extensions\NettePalette;

use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Throwable;

/**
 * Palette Presenter for on demand generation images with palette
 * Class PalettePresenter
 * @package App\Presenters
 */
class PalettePresenter extends Presenter
{
    /** @var Container */
    private $container;


    /**
     * PalettePresenter constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        parent::__construct();
    }


    /**
     * Palette images backend endpoint.
     * @return void
     * @throws Throwable
     */
    public function actionImage(): void
    {

        /** @var Palette $palette */
        $palette = $this->container->getService('palette.service');
        $palette->serverResponse();

        $this->terminate();
    }
}
