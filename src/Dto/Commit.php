<?php


namespace App\Dto;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * This handle commit data
 * Class Commit
 * @package App\Dto
 */
class Commit implements DtoInterface
{
    /**
     * @var string $sha
     * @Assert\NotBlank()
     * @Assert\Length(max="20")
     */
    protected $sha;

    /**
     * @var string $message
     * @Assert\NotBlank()
     */
    protected $message;

    /**
     * Commit constructor.
     * @param string $sha
     * @param string $message
     */
    public function __construct(string $sha, string $message)
    {
        $this->sha = $sha;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getSha(): string
    {
        return $this->sha;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

}
