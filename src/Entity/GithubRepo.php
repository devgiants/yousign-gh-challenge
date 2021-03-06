<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\Uuidable;
use App\Repository\GithubRepoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(
 *     repositoryClass=GithubRepoRepository::class
 * )
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="github_id_index", columns={"github_id"})}
 * )
 * @UniqueEntity(
 *      fields={"githubId"},
 *      message="Repo ID must be unique"
 * )
 */
class GithubRepo implements EntityInterface
{
    use Uuidable;

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
     * @Assert\Unique()
     */
    protected $githubId;

    public function __construct()
    {
        $this->commits = new ArrayCollection();
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
