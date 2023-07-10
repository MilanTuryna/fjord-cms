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

use App\Model\Extensions\NettePalette\Latte\LatteFilter;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Definition;
use Nette\DI\ServiceCreationException;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\FactoryDefinition;

/**
 * Palette Extension for Nette Framework
 * Class PaletteExtension
 * @package Palette
 */
class PaletteExtension extends CompilerExtension
{
    /**
     * Processes configuration data.
     * @return void
     * @throws ServiceCreationException
     */
    public function loadConfiguration(): void
    {
        // Načtení a validace konfigurace rozšíření.
        /** @var array<string, int|string|bool|null> $config */
        $config = $this->getConfig();

        if(!isset($config['path']))
        {
            throw new ServiceCreationException('Missing required path parameter in PaletteExtension configuration');
        }

        if(!isset($config['url']))
        {
            throw new ServiceCreationException('Missing required url parameter in PaletteExtension configuration');
        }

        if(!isset($config['signingKey']))
        {
            throw new ServiceCreationException('Missing required parameter signingKey in PaletteExtension configuration');
        }

        //// Zaregistrování palette služeb.
        $builder = $this->getContainerBuilder();

        // Zaregistrování Nette služby Palette.
        $paletteService = $builder
            ->addDefinition($this->prefix('service'))
            ->setType(Palette::class)
            ->setArguments([
                $config['path'],
                $config['url'],
                empty($config['basepath']) ? NULL : $config['basepath'],
                $config['signingKey'],
                empty($config['fallbackImage']) ? NULL : $config['fallbackImage'],
                empty($config['template']) ? NULL : $config['template'],
                empty($config['fallbackImages']) ? [] : $config['fallbackImages'],
                empty($config['websiteUrl']) ? NULL : $config['websiteUrl'],
                empty($config['pictureLoader']) ? NULL : $config['pictureLoader'],
            ])
            ->addSetup('setHandleExceptions', [
                $config['handleException'] ?? TRUE,
            ]);

        // Nastavení výchozí kvality webp obrázků, které generuje makro n:webp.
        if (isset($config['webpMacroDefaultQuality']))
        {
            $paletteService->addSetup('setWebpMacroDefaultQuality', [$config['webpMacroDefaultQuality']]);
        }

        // Zaregistrování služby Latte filtru.
        $builder
            ->addDefinition($this->prefix('filter'))
            ->setType(LatteFilter::class)
            ->setArguments([$this->prefix('@service')]);

        //// Zaregistrování filtrů a maker do Latte.
        $latteService = $this->getLatteService();
        $latteService
            ->addSetup('?->addProvider(?, ?)', ['@self', 'palette', $paletteService])
            ->addSetup('addFilter', ['palette', $this->prefix('@filter')]);

        //// Zaregistrování vlastního mapování pro PalettePresenter.
        //// (v budoucnu bude nahrazeno za Application->onStartup[])
        /** @var FactoryDefinition|ServiceDefinition $presenterFactory */
        $presenterFactory = $builder->getDefinition('nette.presenterFactory');

        if($presenterFactory instanceof FactoryDefinition)
        {
            $presenterFactory = $presenterFactory->getResultDefinition();
        }

        $presenterFactory
            ->addSetup('setMapping', [['Palette' => 'App\Model\Extensions\NettePalette\*Presenter']]);
    }


    /**
     * Get Latte service definition
     * @return ServiceDefinition
     */
    protected function getLatteService(): Definition
    {
        $builder = $this->getContainerBuilder();

        /** @var ServiceDefinition|FactoryDefinition $service */
        $service = $builder->hasDefinition('nette.latteFactory')
            ? $builder->getDefinition('nette.latteFactory')
            : $builder->getDefinition('nette.latte');

        if($service instanceof FactoryDefinition)
        {
            return $service->getResultDefinition();
        }

        return $service;
    }
}
