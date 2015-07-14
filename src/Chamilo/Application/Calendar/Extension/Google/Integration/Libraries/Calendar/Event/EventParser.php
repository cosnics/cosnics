<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventParser
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    private $renderer;

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Publication
     */
    private $publication;

    /**
     *
     * @var integer
     */
    private $fromDate;

    /**
     *
     * @var integer
     */
    private $toDate;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Publication $publication
     * @param integer $fromDate
     * @param integer $toDate
     */
    public function __construct(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $fromDate, $toDate)
    {
        $this->renderer = $renderer;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     *
     * @return integer
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     *
     * @param integer $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    /**
     *
     * @return integer
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     *
     * @param integer $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getEvents()
    {
        $events = array();

        return $events;
    }
}
