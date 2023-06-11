<?php


namespace App\Forms\Gallery\Data;


use App\Model\Database\Repository\Gallery\Entity\GalleryItem;
use Nette\Http\FileUpload;

/**
 * Class ItemFormData
 * @package App\Forms\Gallery\Data
 */
class ItemFormData extends GalleryItem
{
    // 10MB
    const MAX_FILE_SIZE_MB = 10;
    const COMPRESSED_NAME_LENGTH = 14;

    const MAX_FILE_SIZE = self::MAX_FILE_SIZE_MB*(1024**2);

    public FileUpload $file_upload;

    /**
     * @param string $filename
     * @param string $extension
     * @return string
     */
    public static function encodeName(string $filename, string $extension): string {
        return substr(md5($filename), 0, self::COMPRESSED_NAME_LENGTH - 4) . substr(md5(time()), 0, 4) . "." . $extension;
    }
}