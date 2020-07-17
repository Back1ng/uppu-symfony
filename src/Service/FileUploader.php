<?php


namespace App\Service;


use App\Entity\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $file;
    private $serverName;
    private $directory;

    public function __construct(UploadedFile $file, string $directory, string $serverName)
    {
        $this->file = $file;
        $this->directory = $directory;
        $this->serverName = $serverName;
    }

    public function store() : File
    {
        DirectoryManager::create($this->getDirectory());

        $file = (new File())
            ->setOriginalName($this->getFile()->getClientOriginalName())
            ->setName($this->getServerName().'.'.$this->getFile()->guessClientExtension())
            ->setUploadedPath($this->getDirectory())
            ->setUploadedAt(new \DateTime("now", new \DateTimeZone("UTC")))
            ->setMimeType($this->getFile()->getClientMimeType());

        try {
            $this->getFile()->move($this->getDirectory(), $file->getName());
        } catch (FileException $exception) {
            return $exception->getMessage();
        }

        return $file;
    }

    public function getDirectory() : string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory) : self
    {
        $this->directory = $directory;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getServerName()
    {
        return $this->serverName;
    }

    public function setServerName($serverName): self
    {
        $this->serverName = $serverName;

        return $this;
    }
}