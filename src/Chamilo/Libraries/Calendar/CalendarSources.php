<?php
namespace Chamilo\Libraries\Calendar;

/**
 *
 * @package Chamilo\Libraries\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarSources
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Event\EventSource[]
     */
    private $sources;

    /**
     *
     * @var string[]
     */
    private $sourceKeys;

    public function __construct()
    {
        $this->sources = array();
        $this->sourceKeys = array();
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
     * @return \Chamilo\Libraries\Calendar\Event\EventSource[]
     */
    public function getSources()
    {
        return $this->sources;
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
     * @param \Chamilo\Libraries\Calendar\Event\EventSource $source
     */
    public function addSource($source)
    {
        if (! in_array($source->hash(), $this->getSourceKeys()))
        {
            $this->sources[$source->hash()] = $source;
            $this->sourceKeys[] = $source->hash();
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Event\EventSource $source
     * @return integer
     */
    public function getSourceKey($source)
    {
        $this->addSource($source);
        return array_search($source->hash(), $this->getSourceKeys());
    }

    /**
     * Determine the classes for a specific source
     *
     * @param string $key
     * @param boolean $fade
     * @return string
     */
    public function getSourceClasses($source = null)
    {
        return 'event-container-source event-container-source-' . $this->getSourceKey($source);
    }
}

