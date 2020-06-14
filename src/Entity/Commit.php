<?php

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
     * @ORM\Column(type="string", length=20)
     */
    protected $sha1;

    /**
     * @ORM\ManyToOne(targetEntity=GithubRepo::class, inversedBy="commits")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $githubRepo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSha1(): ?string
    {
        return $this->sha1;
    }

    public function setSha1(string $sha1): self
    {
        $this->sha1 = $sha1;

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
}
