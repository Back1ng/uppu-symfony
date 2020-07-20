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
    }

    /**
     * @param UploadedFile $file
     * @param string $name
     * @return File
     * @throws FileException|\Exception
     */
    public function store(UploadedFile $file, string $name) : File
    {
        DirectoryManager::create($this->directory);

        $fileEntity = (new File())
            ->setOriginalName($file->getClientOriginalName())
            ->setName($nameOnServer = "{$name}.".$file->guessClientExtension())
            ->setUploadedPath($this->directory)
            ->setUploadedAt(new \DateTime("now", new \DateTimeZone("UTC")))
            ->setMimeType($file->getClientMimeType())
            ->setSize($file->getSize());

        $file->move($this->directory, $nameOnServer);
        return $fileEntity;
    }

    // TODO try to set optimal convert, probably need rabbitmq or smth another
//    private function sendToConvert($name, $extension)
//    {
//        if($extension === "webm") {
//            return VideoConverter::convert(
//                $this->directory,
//                $name,
//                $extension
//            );
//        } else {
//            return $name.".".$extension;
//        }
//    }
}