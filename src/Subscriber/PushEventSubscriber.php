<?php

declare(strict_types=1);


namespace App\Subscriber;


use App\Assembler\CommitAssembler;
use App\Assembler\GithubRepoAssembler;
use App\Command\ImportGithubEventsCommand;
use App\Dto\Commit;
use App\Entity\Commit as CommitEntity;
use App\Dto\PushEventPayload;
use App\Entity\GithubRepo as GithubRepoEntity;
use App\Event\GithubArchiveEvents;
use App\Event\LineProcessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class PushEventSubscriber
 * @package App\Subscriber
 */
class PushEventSubscriber implements EventSubscriberInterface, LineProcessEventSubscriberInterface
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * @var Serializer $serializer
     */
    protected $serializer;

    /**
     * @var GithubRepoAssembler $repoAssembler
     */
    protected $repoAssembler;

    /**
     * @var CommitAssembler $commitAssembler
     */
    protected $commitAssembler;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            GithubArchiveEvents::LINE_PROCESS => 'onLineProcess'
        ];
    }

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        GithubRepoAssembler $repoAssembler,
        CommitAssembler $commitAssembler
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->repoAssembler = $repoAssembler;
        $this->commitAssembler = $commitAssembler;
    }

    /**
     * For processing a line from a commit POV
     * @param LineProcessEvent $lineProcessEvent
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function onLineProcess(LineProcessEvent $lineProcessEvent)
    {
        $githubEvent = $lineProcessEvent->getGithubEvent();
        // Handle only push events
        if ($githubEvent->getType() !== $this->getTargetEventType()) {
            return;
        }

        // Handle repo
        /** @var GithubRepoEntity $repoEntity */
        $repoEntity = $this->repoAssembler->getEntity(GithubRepoEntity::class, $githubEvent->getRepo());

        // Retrieve commits data
        // Usage of intermediary PushEventPayload will allow progressive enrichment of data storage in future
        /** @var PushEventPayload $pushEventPayload */
        $pushEventPayload = $this->serializer->denormalize(
            $githubEvent->getPayload(),
            PushEventPayload::class,
            null,
            [AbstractNormalizer::ATTRIBUTES => ['commits', 'pushId']]
        );

        // Get commits
        $commits = $this->serializer->denormalize(
            $pushEventPayload->getCommits(),
            'App\Dto\Commit[]'
        );

        /** @var Commit $commit */
        foreach ($commits as $commit) {
            /** @var CommitEntity $commitEntity */
            $commitEntity = $this->commitAssembler->getEntity(
                CommitEntity::class,
                $commit,
                [
                    'repo' => $repoEntity,
                    'push_id' => $pushEventPayload->getPushId()
                ]
            );

            // Add missing attributes before saving
            $commitEntity
                ->setCreatedAt($githubEvent->getCreatedAt())
                ->setGithubRepo($repoEntity)
                ->setPushId((string)$pushEventPayload->getPushId());
        }

        unset($repoEntity);
        unset($githubEvent);
    }

    /**
     * @inheritDoc
     */
    public function getTargetEventType(): string
    {
        return ImportGithubEventsCommand::TYPE_OPTION_PUSH;
    }
}
