<?php


namespace App\Dto;


use Symfony\Component\Validator\Constraints as Assert;

class GithubRepo
{
    /**
     * @var int
     * @Assert\NotBlank()
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    protected $url;

    /**
     * GithubRepo constructor.
     * @param int $id
     * @param string $name
     * @param string $url
     */
    public function __construct(int $id, string $name, string $url)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

}
