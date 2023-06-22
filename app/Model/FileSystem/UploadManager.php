<?php

namespace App\Model\FileSystem;

use App\Model\Cryptography;
use App\Model\FileSystem\Exceptions\UploadNotValidException;
use Exception;
use FilesystemIterator;
use Nette\FileNotFoundException;
use Nette\Http\FileUpload;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class UploadManager
{
    /**
     * @param string $path
     * @param array $allowedExtension
     */
    public function __construct(protected string $path, protected array $allowedExtension) {
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
            $upload->move($this->path . DIRECTORY_SEPARATOR . $fileName);
            bdump($this->path . DIRECTORY_SEPARATOR . $fileName);
        } else {
            throw new UploadNotValidException();
        }
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public function isFileExist(string $fileName): bool
    {
        return file_exists($this->path . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * @return Finder
     */
    public function getUploads(): Finder
    {
        if (!is_dir($this->path) || !file_exists($this->path)) mkdir($this->path);
        return Finder::findFiles("*")->from($this->path);
    }

    public function deleteUploads() {
        $di = new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ( $ri as $file ) if(!$file->isDir()) unlink($file);
    }

    /**
     * @param $fileName (with extension)
     */
    public function deleteUpload($fileName): void
    {
        bdump($this->path . DIRECTORY_SEPARATOR . $fileName);
        if (file_exists($this->path . DIRECTORY_SEPARATOR . $fileName)) {
            FileSystem::delete($this->path . DIRECTORY_SEPARATOR . $fileName);
        } else {
            throw new FileNotFoundException();
        }
    }
}