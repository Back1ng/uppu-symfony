<?php


namespace App\Service;


use Symfony\Component\Filesystem\Filesystem;

class DirectoryManager
{
    private $directory;

    public function create() : string
    {
        $filesystem = new Filesystem();
        if (! $filesystem->exists($this->getDirectory())) {
            $filesystem->mkdir($this->getDirectory());
        }
        return $this->getDirectory();
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function setDirectory(string $directory) : self
    {
        $this->directory = $directory;

        return $this;
    }
}