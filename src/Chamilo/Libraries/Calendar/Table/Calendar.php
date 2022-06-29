<?php
namespace Chamilo\Libraries\Calendar\Table;

use HTML_Table;

/**
 *
 * @package Chamilo\Libraries\Calendar\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Calendar extends HTML_Table
{
    public const TIME_PLACEHOLDER = '__TIME__';

    private int $displayTime;

    /**
     * @var string[]
     */
    private array $eventsToShow;

    /**
     * @param string[] $classes
     */
    public function __construct(?int $displayTime = null, array $classes = [])
    {
        if (is_null($displayTime))
        {
            $this->displayTime = time();
        }
        else
        {
            $this->displayTime = $displayTime;
        }
        $this->eventsToShow = [];

        array_unshift($classes, 'table-calendar');

        parent::__construct(['class' => implode(' ', $classes), 'cellspacing' => 0]);
    }

    public function addEvent(int $time, string $content)
    {
        $this->eventsToShow[$time][] = $content;
    }

    public function containsEventsForTime(int $time): bool
    {
        return count($this->eventsToShow[$time]) > 0;
    }

    public function getDisplayTime(): int
    {
        return $this->displayTime;
    }

    public function setDisplayTime(int $displayTime)
    {
        $this->displayTime = $displayTime;
    }

    /**
     * Gets the end date which will be displayed by this calendar.
     */
    abstract public function getEndTime(): int;

    /**
     * Gets the list of events to show sorted by their starting time
     */
    public function getEventsToShow(): array
    {
        ksort($this->eventsToShow);

        return $this->eventsToShow;
    }

    /**
     * Gets the first date which will be displayed by this calendar.
     */
    abstract public function getStartTime(): int;
}
