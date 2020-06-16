<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CommitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CommitRepository::class)
 *
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="unique_index", columns={"sha", "github_repo_id", "push_id"})}
 * )
 * @UniqueEntity(
 *      fields={"sha", "github_repo_id", "push_id"},
 *      message="Github repo / SHA / Push ID must be unique"
 * )
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

    /**
     * @ORM\Column(type="string")
     */
    protected $pushId;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getSha(): ?string
    {
        return $this->sha;
    }

    /**
     * @param string $sha
     * @return $this
     */
    public function setSha(string $sha): self
    {
        $this->sha = $sha;

        return $this;
    }

    /**
     * @return GithubRepo|null
     */
    public function getGithubRepo(): ?GithubRepo
    {
        return $this->githubRepo;
    }

    /**
     * @param GithubRepo|null $githubRepo
     * @return $this
     */
    public function setGithubRepo(?GithubRepo $githubRepo): self
    {
        $this->githubRepo = $githubRepo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPushId(): ?string
    {
        return $this->pushId;
    }

    /**
     * @param string $pushId
     * @return $this
     */
    public function setPushId(string $pushId): self
    {
        $this->pushId = $pushId;

        return $this;
    }
}
