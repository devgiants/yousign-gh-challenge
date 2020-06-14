<?php


namespace App\Subscriber;


use App\Assembler\GithubRepoAssembler;
use App\Command\ImportGithubEventsCommand;
use App\Dto\Commit;
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
        GithubRepoAssembler $repoAssembler
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->repoAssembler = $repoAssembler;
    }

    /**
     * For processing a line from a commit POV
     * @param LineProcessEvent $lineProcessEvent
     */
    public function onLineProcess(LineProcessEvent $lineProcessEvent)
    {
        $githubEvent = $lineProcessEvent->getGithubEvent();
        // Handle only push events
        if ($githubEvent->getType() !== $this->getTargetEventType()) {
            return;
        }

        // Handle repo
        $repoEntity = $this->repoAssembler->getEntity(GithubRepoEntity::class, $githubEvent->getRepo());

        // Retrieve commits data
        // Usage of intermediary PushEventPayload will allow progressive enrichment of data storage in future
        /** @var PushEventPayload $pushEventPayload */
        $pushEventPayload = $this->serializer->denormalize(
            $githubEvent->getPayload(),
            PushEventPayload::class,
            null,
            [AbstractNormalizer::ATTRIBUTES => ['commits']]
        );

        // Get commits
        $commits = $this->serializer->denormalize(
            $pushEventPayload->getCommits(),
            'App\Dto\Commit[]'
        );

        dump($commits);
        die();

        $this->entityManager->flush();

        // For memory savings
        $this->entityManager->clear();
        unset($repoEntity);
    }

    /**
     * @inheritDoc
     */
    public function getTargetEventType(): string
    {
        return ImportGithubEventsCommand::TYPE_OPTION_PUSH;
    }
}
