<?php
namespace Chamilo\Application\Calendar\Extension\Google;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarProperties
{

    /**
     *
     * @var string
     */
    private $summary;

    /**
     *
     * @var string
     */
    private $description;

    /**
     *
     * @var string
     */
    private $timeZone;

    /**
     *
     * @param string $summary
     * @param string $description
     * @param string $timeZone
     */
    public function __construct($summary, $description, $timeZone)
    {
        $this->summary = $summary;
        $this->description = $description;
        $this->timeZone = $timeZone;
    }

    /**
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     *
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     *
     * @param string $timeZone
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;
    }
}