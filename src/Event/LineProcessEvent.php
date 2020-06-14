<?php


namespace App\Event;


use App\Dto\GithubEvent;

/**
 * Class LineProcessEvent
 * @package App\Event
 */
class LineProcessEvent
{
    /**
     * @var GithubEvent $githubEvent
     */
    protected $githubEvent;

    /**
     * @param GithubEvent $githubEvent
     * @return self
     */
    public static function createFromGithubEvent(GithubEvent $githubEvent): self
    {
        return new static($githubEvent);
    }

    /**
     * LineProcessEvent constructor.
     * @param GithubEvent $githubEvent
     */
    protected function __construct(GithubEvent $githubEvent)
    {
        $this->githubEvent = $githubEvent;
    }

    /**
     * @return GithubEvent
     */
    public function getGithubEvent(): GithubEvent
    {
        return $this->githubEvent;
    }
}
