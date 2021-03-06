<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user_token;

    /**
     * @ORM\ManyToOne(targetEntity=File::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $comment_parent_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUserToken(): ?string
    {
        return $this->user_token;
    }

    public function setUserToken(string $user_token): self
    {
        $this->user_token = $user_token;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getCommentParentId(): ?int
    {
        return $this->comment_parent_id;
    }

    public function setCommentParentId(int $comment_parent_id): self
    {
        $this->comment_parent_id = $comment_parent_id;

        return $this;
    }
}
