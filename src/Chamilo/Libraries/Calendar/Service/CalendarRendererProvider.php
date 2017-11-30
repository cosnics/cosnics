<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceCalculator;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Interfaces\VisibilitySupport;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CalendarRendererProvider implements CalendarRendererProviderInterface
{
    const SOURCE_TYPE_INTERNAL = 1;
    const SOURCE_TYPE_EXTERNAL = 2;
    const SOURCE_TYPE_BOTH = 3;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string[] $displayParameters;
     */
    public function __construct(User $user, $displayParameters)
    {
        $this->user = $user;
        $this->displayParameters = $displayParameters;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface::getUser()
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface::getDisplayParameters()
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
     * @see \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface::getInternalEventsInPeriod()
     */
    public function getInternalEventsInPeriod($startTime, $endTime, $calculateRecurrence = true)
    {
        return $this->getEvents(self::SOURCE_TYPE_INTERNAL, $startTime, $endTime, $calculateRecurrence);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface::getExternalEventsInPeriod()
     */
    public function getExternalEventsInPeriod($startTime, $endTime, $calculateRecurrence = true)
    {
        return $this->getEvents(self::SOURCE_TYPE_EXTERNAL, $startTime, $endTime, $calculateRecurrence);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface::getAllEventsInPeriod()
     */
    public function getAllEventsInPeriod($startTime, $endTime, $calculateRecurrence = true)
    {
        return $this->getEvents(self::SOURCE_TYPE_BOTH, $startTime, $endTime, $calculateRecurrence);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface::getInternalEvents()
     */
    public function getInternalEvents()
    {
        return $this->getEvents(self::SOURCE_TYPE_INTERNAL);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface::getExternalEvents()
     */
    public function getExternalEvents()
    {
        return $this->getEvents(self::SOURCE_TYPE_EXTERNAL);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface::getAllEvents()
     */
    public function getAllEvents()
    {
        return $this->getEvents(self::SOURCE_TYPE_BOTH);
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
                    $recurrenceCalculator = new RecurrenceCalculator();
                    $parsedEvents = $recurrenceCalculator->getEvents($event, $startTime, $endTime);
                    
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
            $ajaxVisibilityClassName = $this->getVisibilityContext() . '\Ajax\Component\CalendarEventVisibilityComponent';
            
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
     * @param integer $source
     * @return boolean
     */
    public function isInternalSource($source)
    {
        return $this->matchesRequestedSource(self::SOURCE_TYPE_INTERNAL, $source);
    }

    /**
     *
     * @param integer $source
     * @return boolean
     */
    public function isExternalSource($source)
    {
        return $this->matchesRequestedSource(self::SOURCE_TYPE_EXTERNAL, $source);
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