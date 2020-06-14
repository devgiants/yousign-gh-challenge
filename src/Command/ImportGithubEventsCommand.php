<?php

namespace App\Command;

use App\Dto\GithubEvent;
use App\Event\GithubArchiveEvents;
use App\Event\LineProcessEvent;
use App\Exception\DayNotValidException;
use App\Exception\GithubEventNotSupportedException;
use App\Model\Person;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
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
    public const TYPE_OPTION_PUSH = 'PushEvent';

    // Placeholder constants
    public const PLACEHOLDER_CHAR = '%';
    public const YEAR = self::PLACEHOLDER_CHAR . 'Y';
    public const MONTH = self::PLACEHOLDER_CHAR . 'M';
    public const DAY = self::PLACEHOLDER_CHAR . 'D';
    public const HOUR = self::PLACEHOLDER_CHAR . 'H';

    public const TEMP_GZ_FILE_PATH = '/tmp/data.gz';
    public const CURRENT_JSON_FILE_PATH = '/tmp/data.json';

    public const DATA_CHAIN = 'https://data.gharchive.org/' . self::YEAR . '-' . self::MONTH . '-' . self::DAY . '-' . self::HOUR . '.json.gz';

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
     * @var array $eventTypes
     */
    protected $eventTypes;


    /**
     * ImportGithubEventsCommand constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param SerializerInterface $serializer
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, SerializerInterface $serializer)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
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

        if (!$this->dayToRetrieve instanceof \DateTime) {
            throw new DayNotValidException("Day \"{$day}\" is not properly formatted. Use Ymd format");
        }

        // TODO to make evolve when other events will be added
        if ($this->eventTypes !== [static::TYPE_OPTION_PUSH]) {
            throw new GithubEventNotSupportedException('This event type is not supported yet');
        }
    }


    /**
     * This store in temporary files the JSON data
     * @param string $finalDataChain
     */
    protected function provideJsonData(string $finalDataChain)
    {
        // Get data
        $this->io->text('Get data from GitHub...');
        file_put_contents(static::TEMP_GZ_FILE_PATH, file_get_contents($finalDataChain));

        // Extraction
        $this->io->text('Extract...');
        $gzFp = gzopen(static::TEMP_GZ_FILE_PATH, 'rb');
        $jsonFp = fopen(static::CURRENT_JSON_FILE_PATH, 'wb');
        while (!gzeof($gzFp)) {
            fwrite($jsonFp, gzread($gzFp, 4096));
        }
        gzclose($gzFp);
        unlink(static::TEMP_GZ_FILE_PATH);
        fclose($jsonFp);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        $this->checkInputs();

        $this->io->title(sprintf('Start %s retrieve process', implode(', ', $this->eventTypes)));

        // Replace first placeholded data to retrieve elements
        // TODO use sprintf
        $filledDataChain = str_replace(
            [
                static::YEAR,
                static::MONTH,
                static::DAY
            ],
            [
                $this->dayToRetrieve->format('Y'),
                $this->dayToRetrieve->format('m'),
                $this->dayToRetrieve->format('d')
            ],
            static::DATA_CHAIN
        );


        // Loop on data splitted hour by hour to limit volume on each batch
        for ($i = 0; $i <= $this->dayToRetrieve->format('d'); $i++) {
            $this->io->section("Retrieving for {$this->dayToRetrieve->format('d/m/Y')} - {$i}h");

//            try {
//                $this->provideJsonData(str_replace(static::HOUR, $i, $filledDataChain));

            // Loop on results
            $roJsonFp = fopen(static::CURRENT_JSON_FILE_PATH, 'r');

            $this->io->text('Handle...');
            while (false !== ($jsonLine = fgets($roJsonFp))) {
                // Normalize global event with payload untouched
                /** @var GithubEvent $githubEvent */
                $githubEvent = $this->serializer->deserialize($jsonLine, GithubEvent::class, 'json');

                // Use event to allow flexible payload handling
                $this->eventDispatcher->dispatch(
                    LineProcessEvent::createFromGithubEvent($githubEvent),
                    GithubArchiveEvents::LINE_PROCESS
                );
            }
//            } catch (\Exception $exception) {
//            }
        }

        return Command::SUCCESS;
    }
}
