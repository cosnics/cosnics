<?php
namespace Chamilo\Libraries\Calendar\Renderer\Service;

use Chamilo\Libraries\Calendar\Event\RecurrenceCalculator;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class CalendarRendererProvider implements
    \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface
{

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $dataUser;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $viewingUser;

    /**
     *
     * @var string[]
     */
    private $displayParameters;

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     * @param string[] $displayParameters;
     */
    public function __construct(User $dataUser, User $viewingUser, $displayParameters)
    {
        $this->dataUser = $dataUser;
        $this->viewingUser = $viewingUser;
        $this->displayParameters = $displayParameters;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getDataUser()
    {
        return $this->dataUser;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     */
    public function setDataUser(User $dataUser)
    {
        $this->dataUser = $dataUser;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getViewingUser()
    {
        return $this->viewingUser;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     */
    public function setViewingUser(User $viewingUser)
    {
        $this->viewingUser = $viewingUser;
    }

    /**
     *
     * @return string[]
     */
    public function getDisplayParameters()
    {
        return $this->displayParameters;
    }

    /**
     *
     * @param string[] $displayParameters
     */
    public function setDisplayParameters($displayParameters)
    {
        $this->displayParameters = $displayParameters;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getEvents()
     */
    public function getEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $startTime, $endTime)
    {
        $events = $this->aggregateEvents($renderer, $startTime, $endTime);

        $recurringEvents = array();

        foreach ($events as $event)
        {
            $recurrenceCalculator = new RecurrenceCalculator($event, $startTime, $endTime);
            $parsedEvents = $recurrenceCalculator->getEvents();

            foreach ($parsedEvents as $parsedEvent)
            {
                $recurringEvents[] = $parsedEvent;
            }
        }

        return $recurringEvents;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param integer $startTime
     * @param integer $endTime
     */
    abstract public function aggregateEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $startTime,
        $endTime);

    /**
     *
     * @return boolean
     */
    public function supportsVisibility()
    {
        if ($this instanceof VisibilitySupport)
        {
            $ajaxVisibilityClassName = ClassnameUtilities :: getInstance()->getNamespaceParent(
                $this->getVisibilityContext()) . '\Ajax\Component\CalendarEventVisibilityComponent';

            if (! class_exists($ajaxVisibilityClassName))
            {
                throw new \Exception(
                    'Please add an ajax Class CalendarEventVisibilityComponent to your implementing context\'s Ajax subpackage (' .
                         $this->getVisibilityContext() .
                         '). This class should extend the abstract \Chamilo\Libraries\Calendar\Event\Ajax\Component\CalendarEventVisibilityComponent class.');
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @return boolean
     */
    public function supportsActions()
    {
        return $this instanceof ActionSupport;
    }
}