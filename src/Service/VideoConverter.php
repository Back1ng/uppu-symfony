<?php


namespace App\Service;


use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;

class VideoConverter
{
    public static function convert(string $videoDirectory, string $name, string $extension) : string
    {
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open("{$videoDirectory}/{$name}.{$extension}");
        $mp4Format = new X264();

        // Fix for error "Encoding failed : Can't save to X264"
        // See: https://github.com/PHP-FFMpeg/PHP-FFMpeg/issues/310
        $mp4Format->setAudioCodec("libmp3lame");

        $video->save($mp4Format, "{$videoDirectory}/{$name}.mp4");

        return "{$name}.mp4";
    }
}