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

use App\Model\Extensions\NettePalette\Latte\LatteHelpers;
use Palette\EPictureFormat;
use Throwable;
use Tracy\Debugger;
use Palette\Picture;
use Palette\Exception;
use Nette\Utils\Strings;
use Palette\Generator\Server;
use Palette\SecurityException;
use Palette\Generator\IPictureLoader;
use Nette\Application\BadRequestException;

/**
 * Palette service implementation for Nette Framework
 * Class Palette
 * @package NettePalette
 */
class Palette
{
    /** @var Server */
    protected $generator;

    /** @var string|null */
    protected $websiteUrl;

    /** @var bool is used relative urls for images? */
    protected $isUrlRelative;

    /** @var bool|string generator exceptions handling
     * FALSE = exceptions are thrown
     * TRUE = exceptions are begin detailed logged via Tracy\Debugger
     * string = only exception messages are begin logged to specified log file via Tracy\Debugger
     */
    protected $handleExceptions = TRUE;

    /** @var int nastavení výchozí kvality webp obrázků pro makro n:webp. */
    protected $webpMacroDefaultQuality = 100;


    /**
     * Palette constructor.
     * @param string $storagePath absolute or relative path to generated thumbs (and pictures) directory
     * @param string $storageUrl absolute live url to generated thumbs (and pictures) directory
     * @param string|null $basePath absolute path to website root directory
     * @param string $signingKey
     * @param string|null $fallbackImage absolute or relative path to default image.
     * @param array<string, string>|null $templates palette image query templates
     * @param array<string, string> $fallbackImages
     * @param string|null $websiteUrl
     * @param IPictureLoader|null $pictureLoader
     * @throws Exception
     */
    public function __construct(
        string $storagePath,
        string $storageUrl,
        ?string $basePath,
        string $signingKey,
        ?string $fallbackImage = NULL,
        array $templates = NULL,
        array $fallbackImages = [],
        ?string $websiteUrl = NULL,
        IPictureLoader $pictureLoader = NULL
    )
    {
        // Setup image generator instance
        if(!file_exists($storagePath)) mkdir($storagePath);
        $this->generator = new Server($storagePath, $storageUrl, $basePath, $signingKey);

        // Register fallback image
        if($fallbackImage)
        {
            $this->generator->setFallbackImage($fallbackImage);
        }

        // Register and validate named default images.
        $this->generator->setNamedFallbackImages($fallbackImages);

        // Control fallback image definitions.
        if (
            $fallbackImages
            && !$fallbackImage
        )
        {
            throw new Exception(
                'Parameter `fallbackImage` is mandatory when parameter `fallbackImages` is filled.'
            );
        }

        // Register defined image query templates
        if($templates)
        {
            foreach ($templates as $templateName => $templateQuery)
            {
                $this->generator->setTemplateQuery($templateName, $templateQuery);
            }
        }

        // Set website url (optional)
        $this->websiteUrl = $websiteUrl;

        // Is used relative urls for images?
        $this->isUrlRelative =
            !Strings::startsWith($storageUrl, '//') &&
            !Strings::startsWith($storageUrl, 'http://') &&
            !Strings::startsWith($storageUrl, 'https://');

        // Set custom picture loader
        if($pictureLoader)
        {
            $this->generator->setPictureLoader($pictureLoader);
        }
    }


    /**
     * Nastavení výchozí kvality WebP obrázků, které se generují přes makro n:webp.
     * @param int $webpQuality
     * @return void
     * @throws Exception
     */
    public function setWebpMacroDefaultQuality(int $webpQuality): void
    {
        if ($webpQuality <= 0 || $webpQuality > 100)
        {
            throw new Exception('WebpMacroDefaultQuality must be int<1, 100>.');
        }

        $this->webpMacroDefaultQuality = $webpQuality;
    }


    /**
     * Vrací výchozí kvalitu WebP obrázků, které se generují přes makro n:webp.
     * @return int
     */
    public function getWebpMacroDefaultQuality(): int
    {
        return $this->webpMacroDefaultQuality;
    }


    /**
     * Set generator exceptions handling (image generation via url link)
     * FALSE = exceptions are thrown
     * TRUE = exceptions are begin detailed logged via Tracy\Debugger
     * string = only exception messages are begin logged to specified log file via Tracy\Debugger
     * @param bool|string $handleExceptions
     * @throws Exception
     */
    public function setHandleExceptions($handleExceptions): void
    {
        if(is_bool($handleExceptions) || is_string($handleExceptions))
        {
            $this->handleExceptions = $handleExceptions;
        }
        else
        {
            throw new Exception('Invalid value for handleExceptions in configuration');
        }
    }


    /**
     * Get absolute url to image with specified image query string
     * @param string $image
     * @return string|null
     * @throws Exception
     */
    public function __invoke(string $image): ?string
    {
        return $this->generator->loadPicture($image)->getUrl();
    }


    /**
     * Get url to image with specified image query string
     * Supports absolute picture url when is relative generator url set
     * @param string $image
     * @param string|null $imageQuery
     * @param Picture|null $picture
     * @return null|string
     * @throws Exception
     */
    public function getUrl(string $image, ?string $imageQuery = NULL, Picture &$picture = null): ?string
    {
        // Experimental support for absolute picture url when is relative generator url set
        if($imageQuery && Strings::startsWith($imageQuery, '//'))
        {
            $imageQuery = Strings::substring($imageQuery, 2);
            $imageUrl = $this->getPictureGeneratorUrl($image, $imageQuery, $picture);

            if($this->isUrlRelative)
            {
                if($this->websiteUrl)
                {
                    return $this->websiteUrl . $imageUrl;
                }

                return '//' . $_SERVER['SERVER_ADDR'] . $imageUrl;
            }

            return $imageUrl;
        }

        return $this->getPictureGeneratorUrl($image, $imageQuery, $picture);
    }


    /**
     * Vrací informace o obrázku a jeho URL.
     * @param bool $forMacro
     * @param int|null $quality
     * @param string $image
     * @param string $imageQuery
     * @return SourcePicture
     * @throws Exception
     */
    public function getSourcePicture(bool $forMacro, ?int $quality, string $image, string $imageQuery): SourcePicture
    {
        // Validace URL a načtení palette picture.
        $url = $this->getUrl($image, $imageQuery, $picture);

        if (!$url || !$picture)
        {
            throw new Exception('Generate URL failed.');
        }

        // Pokud je obrázek WebP a generujeme URL pro makro aplikujeme na něj také výchozí nastavení kvality.
        // (jedná se o nouzový fallback)
        /** @var Picture $picture */
        if ($forMacro && ($picture->isWebp() || LatteHelpers::getPictureMimeType($picture) === 'image/webp'))
        {
            $imageQuery.= '&Quality;' . ($quality ?? $this->getWebpMacroDefaultQuality());

            $picture = null;

            $url = $this->getUrl($image, $imageQuery, $picture);

            if (!$url || !$picture)
            {
                throw new Exception('Generate URL in WebP fallback failed.');
            }
        }

        // Sestavíme DTO obrázku.
        return new SourcePicture(
            $image,
            $imageQuery,
            $picture,
            $url
        );
    }


    /**
     * Get url to image with specified image query string from generator
     * @param string $image
     * @param string|null $imageQuery
     * @param Picture|null $picture
     * @return null|string
     * @throws Exception
     */
    protected function getPictureGeneratorUrl($image, $imageQuery = NULL, Picture &$picture = null): ?string
    {
        if($imageQuery !== NULL)
        {
            $image .= '@' . $imageQuery;
        }

        $picture = $this->generator->loadPicture($image);

        return $picture->getUrl();
    }


    /**
     * Get Palette picture instance
     * @param string $image
     * @return Picture
     * @throws Exception
     */
    public function getPicture($image): Picture
    {
        return $this->generator->loadPicture($image);
    }


    /**
     * Get Palette generator instance
     * @return Server
     */
    public function getGenerator(): Server
    {
        return $this->generator;
    }


    /**
     * Execute palette service generator backend
     * @throws Throwable
     */
    public function serverResponse(): void
    {
        $requestImageQuery = '';

        try
        {
            // Get image query from url.
            $requestImageQuery = $this->generator->getRequestImageQuery();

            // Process server response.
            $this->generator->serverResponse();
        }
        catch(Throwable $exception)
        {
            // Handle server generating image response exception
            if($this->handleExceptions)
            {
                if ($exception instanceof SecurityException)
                {
                    Debugger::log($exception->getMessage(), 'palette.security');

                    throw new BadRequestException("Image doesn't exist");
                }

                if (is_string($this->handleExceptions))
                {
                    Debugger::log($exception->getMessage(), $this->handleExceptions);
                }
                else
                {
                    Debugger::log($exception, 'palette');
                }
            }
            else
            {
                throw $exception;
            }

            // Return fallback image on exception if fallback image is configured.
            if($this->generator->getFallbackImage())
            {
                /** @var string $paletteQuery */
                $paletteQuery = preg_replace('/.*@(.*)/','@$1', $requestImageQuery);

                $picture = $this->generator->loadPicture($paletteQuery);
                $savePath = $this->generator->getPath($picture);

                if(!file_exists($savePath))
                {
                    $picture->save($savePath);
                }

                $picture->output();
            }

            throw new BadRequestException("Image doesn't exist");
        }
    }
}
