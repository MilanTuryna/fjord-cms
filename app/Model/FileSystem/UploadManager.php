<?php

namespace App\Model\FileSystem;

use App\Model\Cryptography;
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
     * @param callable $errorCallback
     * @param FileUpload[] $fileUploads
     * @throws Exception
     */
    public function add(callable $errorCallback, array $fileUploads)
    {
        $errorUploads = [];
        foreach ($fileUploads as $upload) {
            if ($upload->hasFile() && $upload->isOk() && $upload->isImage()) {
                $upload->move($this->path . Cryptography::createUnique(). "." . pathinfo($upload->getName(), PATHINFO_EXTENSION));
            } else {
                $errorUploads[] = $upload;
            }
        }

        $errorCallback($errorUploads);
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
     * @param $name
     */
    public function deleteUpload($name): void
    {
        if (file_exists($this->path . $name)) {
            FileSystem::delete($this->path . $name);
        } else {
            throw new FileNotFoundException();
        }
    }
}