<?php


namespace App\Forms\Gallery\Data;


use App\Model\Database\Repository\Gallery\Entity\GalleryItem;

class ItemFormData extends GalleryItem
{
    // 10MB
    const MAX_FILE_SIZE = 10*(1024**2);
    const COMPRESSED_NAME_LENGTH = 14;

    public mixed $file_content;

    /**
     * @param string $filename
     * @param string $extension
     * @return string
     */
    public static function encodeName(string $filename, string $extension): string {
        return substr($filename, 0, self::COMPRESSED_NAME_LENGTH);
    }
}