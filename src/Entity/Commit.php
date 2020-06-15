<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CommitRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommitRepository::class)
 */
class Commit implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $sha;

    /**
     * @ORM\ManyToOne(targetEntity=GithubRepo::class, inversedBy="commits")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $githubRepo;

    /**
     * @ORM\Column(type="text")
     */
    protected $message;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSha(): ?string
    {
        return $this->sha;
    }

    public function setSha(string $sha): self
    {
        $this->sha = $sha;

        return $this;
    }

    public function getGithubRepo(): ?GithubRepo
    {
        return $this->githubRepo;
    }

    public function setGithubRepo(?GithubRepo $githubRepo): self
    {
        $this->githubRepo = $githubRepo;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
