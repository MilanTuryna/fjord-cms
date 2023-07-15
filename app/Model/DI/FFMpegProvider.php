<?php


namespace App\Model\DI;

use FFMpeg\FFMpeg;

/**
 * Class FFMpegProvider
 * @package App\Model\DI
 */
class FFMpegProvider
{
    public string $ffmpeg_binaries;
    public string $ffprobe_binaries;
    public int $timeout;
    public int $threads;

    /**
     * FFMpegProvider constructor.
     * @param string $ffmpeg_binaries
     * @param string $ffprobe_binaries
     * @param int $timeout
     * @param int $threads
     */
    public function __construct(string $ffmpeg_binaries, string $ffprobe_binaries, int $timeout = 3600, int $threads = 12) {
         $this->ffmpeg_binaries = $ffmpeg_binaries;
         $this->ffprobe_binaries = $ffprobe_binaries;
         $this->timeout = $timeout;
         $this->threads = $threads;
    }

    /**
     * @return FFMpeg
     */
    public function getInstance(): FFMpeg {
        return FFMpeg::create([
            'ffmpeg.binaries' => $this->ffmpeg_binaries,
            'ffprobe.binaries' => $this->ffprobe_binaries,
            'timeout' => $this->timeout,
            'ffmpeg.threads' => $this->timeout
        ]);
    }
}