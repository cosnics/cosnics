<?php
namespace Chamilo\Libraries\Calendar\Service;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarSourceService
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
     * @return integer
     */
    public function getSourceKey($source)
    {
        if (! in_array($source, $this->getSources()))
        {
            $this->sources[] = $source;
        }

        return array_search($source, $this->getSources());
    }
}

