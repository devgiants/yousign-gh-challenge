<?php

namespace App\Entity;

use App\Repository\GithubRepoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GithubRepoRepository::class)
 */
class GithubRepo implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $url;

    /**
     * @ORM\OneToMany(targetEntity=Commit::class, mappedBy="githubRepo", orphanRemoval=true)
     */
    protected $commits;

    /**
     * @ORM\Column(type="integer")
     */
    protected $githubId;

    public function __construct()
    {
        $this->commits = new ArrayCollection();
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection|Commit[]
     */
    public function getCommits(): Collection
    {
        return $this->commits;
    }

    public function addCommit(Commit $commit): self
    {
        if (!$this->commits->contains($commit)) {
            $this->commits[] = $commit;
            $commit->setGithubRepo($this);
        }

        return $this;
    }

    public function removeCommit(Commit $commit): self
    {
        if ($this->commits->contains($commit)) {
            $this->commits->removeElement($commit);
            // set the owning side to null (unless already changed)
            if ($commit->getGithubRepo() === $this) {
                $commit->setGithubRepo(null);
            }
        }

        return $this;
    }

    public function getGithubId(): ?int
    {
        return $this->githubId;
    }

    public function setGithubId(int $githubId): self
    {
        $this->githubId = $githubId;

        return $this;
    }
}
