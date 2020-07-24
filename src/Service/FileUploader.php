<?php


namespace App\Service;


use App\Entity\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;

        DirectoryManager::create($this->directory);
    }

    /**
     * @param UploadedFile $file
     * @param string $name
     * @return File
     * @throws FileException|\Exception
     */
    public function store(UploadedFile $file, string $name) : File
    {
        $nameOnServer = "{$name}.".$file->guessClientExtension();

        $fileEntity = (new File())
            ->setOriginalName($file->getClientOriginalName())
            ->setName        ($nameOnServer)
            ->setUploadedPath($this->directory)
            ->setUploadedAt  (new \DateTime("now", new \DateTimeZone("UTC")))
            ->setMimeType    ($file->getClientMimeType())
            ->setSize        ($file->getSize());

        $file->move($this->directory, $nameOnServer);
        return $fileEntity;
    }
}