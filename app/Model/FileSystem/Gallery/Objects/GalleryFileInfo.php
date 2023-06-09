<?php

namespace App\Model\FileSystem\Gallery\Objects;

class GalleryFileInfo
{
    const raw_size = "raw_size", size = "size", size_unit = "size_unit", file_count = "file_count";

    public float $raw_size;
    public string $file_count;
}