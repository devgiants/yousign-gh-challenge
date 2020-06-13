<?php


namespace App\Subscriber;


use App\Command\ImportGithubEventsCommand;
use App\Event\GithubArchiveEvents;
use App\Event\LineProcessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PushEventSubscriber implements EventSubscriberInterface, LineProcessEventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            GithubArchiveEvents::LINE_PROCESS => 'onLineProcess'
        ];
    }

    public function onLineProcess(LineProcessEvent $lineProcessEvent)
    {
        echo 'test';
    }

    /**
     * @inheritDoc
     */
    public function getTargetEventType(): string
    {
        return ImportGithubEventsCommand::TYPE_OPTION_PUSH;
    }
}
