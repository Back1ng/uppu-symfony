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
        DirectoryManager::create($this->directory);

        $file = (new File())
            ->setOriginalName($this->file->getClientOriginalName())
            ->setName($this->serverName.'.'.$this->file->guessClientExtension())
            ->setUploadedPath($this->directory)
            ->setUploadedAt(new \DateTime("now", new \DateTimeZone("UTC")))
            ->setMimeType($this->file->getClientMimeType());

        try {
            $this->file->move($this->directory, $file->getName());
        } catch (FileException $exception) {
            return $exception->getMessage();
        }

        return $file;
    }
}