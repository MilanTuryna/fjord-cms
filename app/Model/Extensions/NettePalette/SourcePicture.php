<?php declare(strict_types=1);

namespace App\Model\Extensions\NettePalette;

use Palette\Picture;

/**
 * Class SourcePicture
 * @package NettePalette
 */
final class SourcePicture
{
    /** @var string */
    private $image;

    /** @var string */
    private $imageQuery;

    /** @var Picture */
    private $picture;

    /** @var string */
    private $pictureUrl;


    /**
     * SourcePicture constructor.
     * @param string $image
     * @param string $imageQuery
     * @param Picture $picture
     * @param string $pictureUrl
     */
    public function __construct(string $image, string $imageQuery, Picture $picture, string $pictureUrl)
    {
        $this->image = $image;
        $this->imageQuery = $imageQuery;
        $this->picture = $picture;
        $this->pictureUrl = $pictureUrl;
    }


    /**
     * Vrací cestu k obrázku zadanou při volání palette.
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }


    /**
     * Vrací palette query zadané při volání palette.
     * @return string
     */
    public function getImageQuery(): string
    {
        return $this->imageQuery;
    }


    /**
     * Vrací Palette obrázek.
     * @return Picture
     */
    public function getPicture(): Picture
    {
        return $this->picture;
    }


    /**
     * Vrací vygenerovanou adresu k obrázku.
     * @return string
     */
    public function getPictureUrl(): string
    {
        return $this->pictureUrl;
    }
}
