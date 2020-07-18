<?php


namespace App\Service;


use App\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $serverName;
    private $directory;

    public function __construct(string $directory, string $serverName)
    {
        $this->directory = $directory;
        $this->serverName = $serverName;
    }

    public function store(UploadedFile $file) : File
    {
        DirectoryManager::create($this->directory);

        $fileEntity = (new File())
            ->setOriginalName($file->getClientOriginalName())
            ->setName($this->serverName.'.'.$file->guessClientExtension())
            ->setUploadedPath($this->directory)
            ->setUploadedAt(new \DateTime("now", new \DateTimeZone("UTC")))
            ->setMimeType($file->getClientMimeType())
            ->setSize($file->getSize());

        $file->move($this->directory, $fileEntity->getName());
        return $fileEntity;
    }
}