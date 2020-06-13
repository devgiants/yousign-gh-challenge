<?php


namespace App\Event;


/**
 * Class LineProcessEvent
 * @package App\Event
 */
class LineProcessEvent
{
    /**
     * @var string $jsonLine
     */
    protected $jsonLine;

    /**
     * @param string $jsonLine
     * @return self
     */
    public static function createFromJsonLine(string $jsonLine): self
    {
        return new static($jsonLine);
    }

    /**
     * LineProcessEvent constructor.
     * @param string $jsonLine
     */
    protected function __construct(string $jsonLine)
    {
        $this->jsonLine = $jsonLine;
    }

    /**
     * @return string
     */
    public function getJsonLine(): string
    {
        return $this->jsonLine;
    }
}
