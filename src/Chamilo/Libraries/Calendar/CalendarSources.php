<?php
namespace Chamilo\Libraries\Calendar;

use Chamilo\Libraries\Calendar\Event\EventSource;

/**
 *
 * @package Chamilo\Libraries\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarSources
{

    /**
     *
     * @var string[]
     */
    private $sources;

    /**
     *
     * @var string[]
     */
    private $sourceKeys;

    public function __construct()
    {
        $this->sources = [];
        $this->sourceKeys = [];
    }

    /**
     *
     * @return string[]
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     *
     * @return string[]
     */
    public function getSourceKeys()
    {
        return $this->sourceKeys;
    }

    /**
     *
     * @return boolean
     */
    public function hasSources()
    {
        return count($this->getSources()) > 0;
    }

    /**
     *
     * @return boolean
     */
    public function hasMultipleSources()
    {
        return count($this->getSources()) > 1;
    }

    /**
     *
     * @param string $source
     */
    public function addSource(EventSource $eventSource)
    {
        if (! in_array($eventSource->hash(), $this->getSourceKeys()))
        {
            $this->sources[$eventSource->hash()] = $eventSource;
            $this->sourceKeys[] = $eventSource->hash();
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\EventSource $source
     * @return integer
     */
    public function getSourceKey(EventSource $eventSource)
    {
        $this->addSource($eventSource);
        return array_search($eventSource->hash(), $this->getSourceKeys());
    }

    /**
     * Determine the classes for a specific source
     *
     * @param \Chamilo\Libraries\Calendar\Event\EventSource $eventSource
     * @return string
     */
    public function getSourceClasses(EventSource $eventSource)
    {
        return 'event-container-source event-container-source-' . $this->getSourceKey($eventSource);
    }
}

