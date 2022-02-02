<?php

namespace App\Entity;

use App\Repository\FileRepository;
use App\Service\Writer\SizeWriter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
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

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $count_downloads = 0;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="file", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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

    public function getCountDownloads(): ?int
    {
        return $this->count_downloads;
    }

    public function setCountDownloads(string $countDownloads): self
    {
        $this->count_downloads = $countDownloads;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setFile($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getFile() === $this) {
                $comment->setFile(null);
            }
        }

        return $this;
    }

    public function getConvertedSize(): string
    {
        $writer = new SizeWriter();
        return $writer->write($this->getSize());
    }
}
