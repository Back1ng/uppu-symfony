<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $original_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uploaded_path;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploaded_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mime_type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $size;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->original_name;
    }

    public function setOriginalName(string $original_name): self
    {
        $this->original_name = $original_name;

        return $this;
    }

    public function getUploadedPath(): ?string
    {
        return $this->uploaded_path;
    }

    public function setUploadedPath(string $uploaded_path): self
    {
        $this->uploaded_path = $uploaded_path;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploaded_at;
    }

    public function setUploadedAt(\DateTimeInterface $uploaded_at): self
    {
        $this->uploaded_at = $uploaded_at;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    public function setMimeType(string $mime_type): self
    {
        $this->mime_type = $mime_type;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }
}
