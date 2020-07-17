<?php


namespace App\Service;


use Symfony\Component\Filesystem\Filesystem;

class DirectoryManager
{
    public static function create(string $path) : string
    {
        $filesystem = new Filesystem();
        if (! $filesystem->exists($path)) {
            $filesystem->mkdir($path);
        }
        return $path;
    }
}