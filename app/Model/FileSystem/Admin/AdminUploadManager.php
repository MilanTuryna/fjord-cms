<?php


namespace App\Model\FileSystem\Admin;


use App\Model\FileSystem\UploadManager;
use JetBrains\PhpStorm\Pure;
use SplFileInfo;

/**
 * Class AdminUploadManager
 * @package App\Model\FileSystem\Admin
 */
class AdminUploadManager extends UploadManager
{
    /**
     * AdminUploadManager constructor.
     * @param AdminUploadDataProvider $dataProvider
     */
    #[Pure] public function __construct(private AdminUploadDataProvider $dataProvider)
    {
        parent::__construct($this->dataProvider->localPath);
    }

    /**
     * @return array
     */
    public function getURLUploads(): array
    {
        $realPathUploads = $this->getUploads();
        $arr = [];
        foreach ($realPathUploads as $upload) {
            /**
             * @var SplFileInfo $upload
             */
            $arr[$this->dataProvider->outsidePath . "/" . $upload->getFilename()] = $upload;
        }
        return $arr;
    }
}