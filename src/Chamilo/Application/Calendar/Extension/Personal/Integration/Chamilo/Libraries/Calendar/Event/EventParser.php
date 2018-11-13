<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Redirect;

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
     * @var \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider
     */
    private $calendarRendererProvider;

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
     * @param \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer $fromDate
     * @param integer $toDate
     */
    public function __construct(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider,
        Publication $publication, $fromDate, $toDate
    )
    {
        $this->calendarRendererProvider = $calendarRendererProvider;
        $this->publication = $publication;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Renderer
     */
    public function getCalendarRendererProvider()
    {
        return $this->calendarRendererProvider;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     */
    public function setCalendarRendererProvider(
        \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider $calendarRendererProvider
    )
    {
        $this->calendarRendererProvider = $calendarRendererProvider;
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
        $events = array();

        $publisher = $this->getPublication()->get_publisher();
        $publishingUser = $this->getUserService()->findUserByIdentifier($this->getPublication()->get_publisher());

        $parser = \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\EventParser::factory(
            $this->getPublication()->get_publication_object(), $this->getFromDate(), $this->getToDate(),
            Event::class_name()
        );

        $parsedEvents = $parser->getEvents();
        foreach ($parsedEvents as &$parsedEvent)
        {
            if ($publisher != $this->getCalendarRendererProvider()->getViewingUser()->getId())
            {
                $parsedEvent->setTitle($parsedEvent->getTitle() . ' [' . $publishingUser->get_fullname() . ']');
            }

            $parsedEvent->setId($this->getPublication()->get_id());
            $parsedEvent->setContext(\Chamilo\Application\Calendar\Extension\Personal\Manager::context());

            $parameters = array();
            $parameters[Application::PARAM_CONTEXT] =
                \Chamilo\Application\Calendar\Extension\Personal\Manager::context();
            $parameters[\Chamilo\Application\Calendar\Extension\Personal\Manager::PARAM_ACTION] =
                \Chamilo\Application\Calendar\Extension\Personal\Manager::ACTION_VIEW;
            $parameters[\Chamilo\Application\Calendar\Extension\Personal\Manager::PARAM_PUBLICATION_ID] =
                $this->getPublication()->getId();

            $redirect = new Redirect($parameters);
            $parsedEvent->setUrl($redirect->getUrl());

            $events[] = $parsedEvent;
        }

        return $events;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    private function getUserService()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $container->get(UserService::class);
    }
}
