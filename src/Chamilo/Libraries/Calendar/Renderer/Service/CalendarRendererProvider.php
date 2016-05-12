<?php
namespace Chamilo\Libraries\Calendar\Renderer\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceCalculator;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;

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
    const SOURCE_TYPE_INTERNAL = 1;
    const SOURCE_TYPE_EXTERNAL = 2;
    const SOURCE_TYPE_BOTH = 3;

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
     * @var \Chamilo\Libraries\Calendar\Event\Event[]
     */
    private $events;

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
     * Get the internal events between $start_time and $end_time
     *
     * @param int $startTime
     * @param int $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEventsInPeriod($startTime, $endTime, $calculateRecurrence = true)
    {
        return $this->getEvents(self :: SOURCE_TYPE_INTERNAL, $startTime, $endTime, $calculateRecurrence);
    }

    /**
     * Get the external events between $start_time and $end_time
     *
     * @param int $startTime
     * @param int $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getExternalEventsInPeriod($startTime, $endTime, $calculateRecurrence = true)
    {
        return $this->getEvents(self :: SOURCE_TYPE_EXTERNAL, $startTime, $endTime, $calculateRecurrence);
    }

    /**
     * Get the events between $start_time and $end_time
     *
     * @param int $startTime
     * @param int $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEventsInPeriod($startTime, $endTime, $calculateRecurrence = true)
    {
        return $this->getEvents(self :: SOURCE_TYPE_BOTH, $startTime, $endTime, $calculateRecurrence);
    }

    /**
     * Get the internal events
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEvents()
    {
        return $this->getEvents(self :: SOURCE_TYPE_INTERNAL);
    }

    /**
     * Get the external events
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getExternalEvents()
    {
        return $this->getEvents(self :: SOURCE_TYPE_EXTERNAL);
    }

    /**
     * Get the events
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEvents()
    {
        return $this->getEvents(self :: SOURCE_TYPE_BOTH);
    }

    /**
     *
     * @param integer $sourceType
     * @param integer $startTime
     * @param integer $endTime
     * @param boolean $calculateRecurrence
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    private function getEvents($sourceType, $startTime = null, $endTime = null, $calculateRecurrence = false)
    {
        $cacheIdentifier = md5(serialize(array($sourceType, $startTime, $endTime, $calculateRecurrence)));

        if (! isset($this->events[$cacheIdentifier]))
        {
            $events = $this->aggregateEvents($sourceType, $startTime, $endTime);

            if ($startTime && $endTime && $calculateRecurrence)
            {
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

                $this->events[$cacheIdentifier] = $recurringEvents;
            }
            else
            {
                $this->events[$cacheIdentifier] = $events;
            }
        }

        return $this->events[$cacheIdentifier];
    }

    /**
     *
     * @param integer $sourceType
     * @param integer $startTime
     * @param integer $endTime
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    abstract public function aggregateEvents($sourceType, $startTime, $endTime);

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

    /**
     *
     * @param integer $source
     * @return boolean
     */
    public function isInternalSource($source)
    {
        return $this->matchesRequestedSource(self :: SOURCE_TYPE_INTERNAL, $source);
    }

    /**
     *
     * @param integer $source
     * @return boolean
     */
    public function isExternalSource($source)
    {
        return $this->matchesRequestedSource(self :: SOURCE_TYPE_EXTERNAL, $source);
    }

    /**
     *
     * @param integer $requestedSource
     * @param integer $implementationSource
     * @return boolean
     */
    public function matchesRequestedSource($requestedSource, $implementationSource)
    {
        return (boolean) ($requestedSource & $implementationSource);
    }
}