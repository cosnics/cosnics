<?php
namespace Chamilo\Libraries\Calendar;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarSources
{

    /**
     *
     * @var string[]
     */
    private $sources;

    public function __construct()
    {
        $this->sources = array();
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
     * @param string[] $sources
     */
    public function setSources($sources)
    {
        $this->sources = $sources;
    }

    /**
     *
     * @param string $source
     */
    public function addSource($source)
    {
        if (! in_array($source, $this->getSources()))
        {
            $this->sources[] = $source;
        }
    }

    /**
     *
     * @param string $source
     * @return integer
     */
    public function getSourceKey($source)
    {
        $this->addSource($source);
        return array_search($source, $this->getSources());
    }

    /**
     * Determine the classes for a specific source
     *
     * @param string $key
     * @param boolean $fade
     * @return string
     */
    public function getSourceClasses($source = null, $fade = false)
    {
        $classes = 'event-container-source event-container-source-' . $this->getSourceKey($source);

        if ($fade)
        {
            $classes .= ' event-container-source-faded';
        }

        return $classes;
    }
}

