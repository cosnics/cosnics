<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event
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
     * @var \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication
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
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer $fromDate
     * @param integer $toDate
     */
    public function __construct(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, Publication $publication,
        $fromDate, $toDate)
    {
        $this->renderer = $renderer;
        $this->publication = $publication;
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
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     */
    public function setPublication($publication)
    {
        $this->publication = $publication;
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
        $publisher = $this->getPublication()->get_publisher();
        $publishingUser = $this->getPublication()->get_publication_publisher();

        $parser = \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\EventParser :: factory(
            $this->getPublication()->get_publication_object(),
            $this->getFromDate(),
            $this->getToDate(),
            Event :: class_name());

        foreach ($parser->get_events() as &$parsed_event)
        {
            if ($publisher != $this->getRenderer()->get_application()->get_user_id())
            {
                $parsed_event->set_title($parsed_event->get_title() . ' [' . $publishingUser->get_fullname() . ']');
            }

            $parsed_event->set_id($this->getPublication()->get_id());
            $parsed_event->set_context(\Chamilo\Application\Calendar\Extension\Personal\Manager :: context());

            $parameters = array();
            $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Calendar\Extension\Personal\Manager :: context();
            $parameters[Application :: PARAM_ACTION] = \Chamilo\Application\Calendar\Extension\Personal\Manager :: ACTION_VIEW;
            $parameters[\Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_PUBLICATION_ID] = $this->getPublication()->get_id();
            $parsed_event->set_url($this->getRenderer()->get_application()->get_url($parameters));

            $events[] = $parsed_event;
        }

        return $events;
    }
}
