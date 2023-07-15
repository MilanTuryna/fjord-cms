<?php


namespace App\Model\FileSystem;


class FileUtils
{
    /**
     * @param string $fileName
     * @return string|bool
     */
    public static function getExtension(string $fileName): string|bool
    {
        $exploded = explode(".", $fileName);
        return end($exploded);
    }

    /**
     * @param $filename
     */
    public static function checkExif($filename) {
            $exif = @exif_read_data($filename);
            if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if ($exif['Orientation']==3 OR $exif['Orientation']==6 OR $exif['Orientation']==8) {
                    $imageResource = imagecreatefromjpeg($filename);
                    $image = match ($exif['Orientation']) {
                        3 => imagerotate($imageResource, 180, 0),
                        6 => imagerotate($imageResource, -90, 0),
                        8 => imagerotate($imageResource, 90, 0),
                    };
                    imagejpeg($image, $filename);
                    imagedestroy($imageResource);
                    imagedestroy($image);
                }
            }
        }
}