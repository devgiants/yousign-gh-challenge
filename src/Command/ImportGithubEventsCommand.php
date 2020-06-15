<?php

declare(strict_types=1);

namespace App\Command;

use App\Dto\GithubEvent;
use App\Event\GithubArchiveEvents;
use App\Event\LineProcessEvent;
use App\Exception\DayNotValidException;
use App\Exception\GithubEventNotSupportedException;
use App\Exception\HourNotValidException;
use App\Provider\JsonDataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ImportCommitsCommand
 * @package App\Command
 */
class ImportGithubEventsCommand extends Command
{

    // Command option
    public const DAY_OPTION_NAME = 'day';
    public const TYPE_OPTION_NAME = 'type';
    public const HOUR_OPTION_NAME = 'hour';
    public const TYPE_OPTION_PUSH = 'PushEvent';


    public const DATA_CHAIN = 'https://data.gharchive.org/%s-%s-%s-%s.json.gz';

    public const BATCH_SIZE = 500;

    protected static $defaultName = 'app:import:github_events';


    /**
     * @var EventDispatcherInterface $eventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var Serializer $serializer
     */
    protected $serializer;

    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * @var JsonDataProvider $jsonDataProvder
     */
    protected $jsonDataProvider;

    /**
     * @var InputInterface $input
     */
    protected $input;

    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var SymfonyStyle $io
     */
    protected $io;

    /**
     * @var \DateTime $dayToRetrieve the day wanted for the current import
     */
    protected $dayToRetrieve;

    /**
     * @var int
     */
    protected $hourToRetrieve;

    /**
     * @var array $eventTypes
     */
    protected $eventTypes;


    /**
     * ImportGithubEventsCommand constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param JsonDataProvider $jsonDataProvider
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        JsonDataProvider $jsonDataProvider
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->jsonDataProvider = $jsonDataProvider;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('This allow to gather commits for one given day (from Github Archives)')
            ->addOption(
                static::DAY_OPTION_NAME,
                'd',
                InputOption::VALUE_REQUIRED,
                'the day you want to retrieve commits from. Format expected Ymd',
                (new \DateTime())->format('Ymd')
            )
            ->addOption(
                static::HOUR_OPTION_NAME,
                'hh',
                InputOption::VALUE_REQUIRED,
                'the hour you want to retrieve commits from. 0-23',
                0
            )
            ->addOption(
                static::TYPE_OPTION_NAME,
                't',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'the event type you want to retrieve. "pushEvent" only defined so far',
                [static::TYPE_OPTION_PUSH]
            );
    }

    /**
     * This will enforce checks on inputs
     */
    protected function checkInputs()
    {
        $day = $this->input->getOption(static::DAY_OPTION_NAME);
        $this->eventTypes = $this->input->getOption(static::TYPE_OPTION_NAME);
        $this->dayToRetrieve = \DateTime::createFromFormat('Ymd', $day);
        $this->hourToRetrieve = $this->input->getOption(static::HOUR_OPTION_NAME);

        if (!$this->dayToRetrieve instanceof \DateTime) {
            throw new DayNotValidException("Day \"{$day}\" is not properly formatted. Use Ymd format");
        }

        if (!is_numeric($this->hourToRetrieve) || $this->hourToRetrieve < 0 || $this->hourToRetrieve > 23) {
            throw new HourNotValidException("Hour \"{$this->hourToRetrieve}\" is not properly formatted. 0-23");
        }


        // TODO to make evolve when other events will be added
        if ($this->eventTypes !== [static::TYPE_OPTION_PUSH]) {
            throw new GithubEventNotSupportedException('This event type is not supported yet');
        }
    }


    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (ob_get_level()) {
            ob_end_clean();
        }
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        $this->checkInputs();

        $this->io->title(sprintf('Start %s retrieve process', implode(', ', $this->eventTypes)));


        $filledDataChain = sprintf(
            static::DATA_CHAIN,
            $this->dayToRetrieve->format('Y'),
            $this->dayToRetrieve->format('m'),
            $this->dayToRetrieve->format('d'),
            $this->hourToRetrieve
        );

        $this->io->section("Retrieving for {$this->dayToRetrieve->format('d/m/Y')} - {$this->hourToRetrieve}h");

        try {
            $this->io->text('Get data from GitHub and extract...');
            ($this->jsonDataProvider)($filledDataChain);


            $this->io->text('Handle...');
            foreach ($this->getLines(JsonDataProvider::CURRENT_JSON_FILE_PATH) as $n => $jsonLine) {
                // Normalize global event with payload untouched
                /** @var GithubEvent $githubEvent */
                $githubEvent = $this->serializer->deserialize($jsonLine, GithubEvent::class, 'json');

                // Use event to allow flexible payload handling
                $this->eventDispatcher->dispatch(
                    LineProcessEvent::createFromGithubEvent($githubEvent),
                    GithubArchiveEvents::LINE_PROCESS
                );
                unset($githubEvent);
                unset($jsonLine);


                if ($n % static::BATCH_SIZE == 0) {
                    // TODO use progress bar instead
                    $this->io->text($this->convert(memory_get_usage(true)) . ' - ' . $n);

                    // For memory savings
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                    flush();
                    gc_collect_cycles();
                }
            }

            $this->entityManager->flush();
            $this->entityManager->clear();
            flush();
            gc_collect_cycles();
        } catch (\Exception $exception) {
            // TODO elaborate
            throw $exception;
        }

        return Command::SUCCESS;
    }

    /**
     * @param $size
     * @return string
     * TODO : externalize in Convert utility
     */
    protected function convert($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    /**
     * To limit memory consumption by yelding line instead of building an array
     * @param $file
     * @return \Generator
     * TODO : externalize in Memory utility
     */
    protected function getLines($file)
    {
        $f = fopen($file, 'r');

        try {
            while ($line = fgets($f)) {
                yield $line;
            }
        } finally {
            fclose($f);
        }
    }
}
