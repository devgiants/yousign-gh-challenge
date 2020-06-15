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

    public function __construct(array $commits)
    {
        $this->commits = $commits;
    }

    /**
     * @return array
     */
    public function getCommits(): array
    {
        return $this->commits;
    }


}
