<?php

declare(strict_types=1);


namespace App\Dto;

use App\Dto\Commit;

/**
 * This DTO carries PushEvent payload, in order to be handled for storing commits
 * Class PushEventPayload
 * @package App\Dto
 */
class PushEventPayload implements DtoInterface
{
    /**
     * @var array $commits
     */
    protected $commits;

    /**
     * @var int $pushId
     */
    protected $pushId;

    public function __construct(array $commits, int $pushId)
    {
        $this->commits = $commits;
        $this->pushId = $pushId;
    }

    /**
     * @return int
     */
    public function getPushId(): int
    {
        return $this->pushId;
    }

    /**
     * @return array
     */
    public function getCommits(): array
    {
        return $this->commits;
    }


}
