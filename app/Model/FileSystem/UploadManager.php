<?php

namespace App\Model\FileSystem;

use App\Model\Cryptography;
use App\Model\FileSystem\Exceptions\UploadNotValidException;
use Exception;
use Nette\FileNotFoundException;
use Nette\Http\FileUpload;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;

class UploadManager
{
    private string $path;

    /**
     * @param string $path
     */
    public function __construct(string $path) {
        $this->path = $path;
    }

    /**
     * @param FileUpload $upload
     * @param string $fileName
     * @return void
     * @throws Exception
     */
    public function add(FileUpload $upload, string $fileName): void
    {
        if($upload->hasFile() && $upload->isOk()) {
            $upload->move($this->path . $fileName);
        } else {
            throw new UploadNotValidException();
        }
    }

    /**
     * @return Finder
     */
    public function getUploads(): Finder
    {
        if (!is_dir($this->path) || !file_exists($this->path)) mkdir($this->path);
        return Finder::findFiles("*")->from($this->path);
    }

    /**
     * @param $fileName (with extension)
     */
    public function deleteUpload($fileName): void
    {
        if (file_exists($this->path . $fileName)) {
            FileSystem::delete($this->path . $fileName);
        } else {
            throw new FileNotFoundException();
        }
    }
}