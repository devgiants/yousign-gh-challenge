<?php


namespace App\Dto;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * This DTO carries generic data around Github event
 * Class GithubEvent
 * @package App\Dto
 */
class GithubEvent
{
    /**
     * @var string $id
     * @Assert\NotBlank()
     */
    protected $id;

    /**
     * @var string $type
     * @Assert\NotBlank()
     */
    protected $type;


    /**
     * @var GithubRepo $repo
     * @Assert\NotBlank()
     */
    protected $repo;

    /**
     * @var array $payload
     * @Assert\NotBlank()
     */
    protected $payload;


    /**
     * GithubEvent constructor.
     * @param string $id
     * @param string $type
     * @param GithubRepo $repo
     * @param array $payload
     */
    public function __construct(string $id, string $type, GithubRepo $repo, array $payload)
    {
        $this->id = $id;
        $this->type = $type;
        $this->repo = $repo;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return GithubRepo
     */
    public function getRepo(): GithubRepo
    {
        return $this->repo;
    }


    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

}
