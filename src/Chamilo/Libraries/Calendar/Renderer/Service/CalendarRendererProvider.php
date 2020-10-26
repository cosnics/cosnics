<?php
namespace Chamilo\Libraries\Calendar\Renderer\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\RecurrenceCalculator;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CalendarRendererProvider implements CalendarRendererProviderInterface
{
    const SOURCE_TYPE_BOTH = 3;
    const SOURCE_TYPE_EXTERNAL = 2;
    const SOURCE_TYPE_INTERNAL = 1;

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
     * @var \Chamilo\Libraries\Calendar\Event\Event[][]
     */
    private $events;

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     * @param string[] $displayParameters ;
     */
    public function __construct(User $dataUser, User $viewingUser, $displayParameters)
    {
        $this->dataUser = $dataUser;
        $this->viewingUser = $viewingUser;
        $this->displayParameters = $displayParameters;
    }

    /**
     *
     * @param integer $sourceType
     * @param integer $startTime
     * @param integer $endTime
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    abstract public function aggregateEvents($sourceType, $startTime, $endTime);

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getAllEvents()
     */
    public function getAllEvents()
    {
        return $this->getEvents(self::SOURCE_TYPE_BOTH);
    }

    /**
     * @param integer $startTime
     * @param integer $endTime
     * @param boolean $calculateRecurrence
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event|\Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEventsInPeriod($startTime, $endTime, $calculateRecurrence = true)
    {
        return $this->getEvents(self::SOURCE_TYPE_BOTH, $startTime, $endTime, $calculateRecurrence);
    }

    /**
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
     * @param integer $sourceType
     * @param integer $startTime
     * @param integer $endTime
     * @param boolean $calculateRecurrence
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    private function getEvents($sourceType, $startTime = null, $endTime = null, $calculateRecurrence = false)
    {
        $cacheIdentifier = md5(serialize(array($sourceType, $startTime, $endTime, $calculateRecurrence)));

        if (!isset($this->events[$cacheIdentifier]))
        {
            $events = $this->aggregateEvents($sourceType, $startTime, $endTime);

            if ($startTime && $endTime && $calculateRecurrence)
            {
                $recurrenceCalculator = new RecurrenceCalculator($events, $startTime, $endTime);
                $this->events[$cacheIdentifier] = $recurrenceCalculator->expandEvents();
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
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getExternalEvents()
     */
    public function getExternalEvents()
    {
        return $this->getEvents(self::SOURCE_TYPE_EXTERNAL);
    }

    /**
     * @param integer $startTime
     * @param integer $endTime
     * @param boolean $calculateRecurrence
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getExternalEventsInPeriod($startTime, $endTime, $calculateRecurrence = true)
    {
        return $this->getEvents(self::SOURCE_TYPE_EXTERNAL, $startTime, $endTime, $calculateRecurrence);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEvents()
    {
        return $this->getEvents(self::SOURCE_TYPE_INTERNAL);
    }

    /**
     * @param integer $startTime
     * @param integer $endTime
     * @param boolean $calculateRecurrence
     *
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEventsInPeriod($startTime, $endTime, $calculateRecurrence = true)
    {
        return $this->getEvents(self::SOURCE_TYPE_INTERNAL, $startTime, $endTime, $calculateRecurrence);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getViewingUser()
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
     * @param integer $source
     *
     * @return boolean
     */
    public function isExternalSource($source)
    {
        return $this->matchesRequestedSource(self::SOURCE_TYPE_EXTERNAL, $source);
    }

    /**
     *
     * @param integer $source
     *
     * @return boolean
     */
    public function isInternalSource($source)
    {
        return $this->matchesRequestedSource(self::SOURCE_TYPE_INTERNAL, $source);
    }

    /**
     *
     * @param integer $requestedSource
     * @param integer $implementationSource
     *
     * @return boolean
     */
    public function matchesRequestedSource($requestedSource, $implementationSource)
    {
        return (boolean) ($requestedSource & $implementationSource);
    }
}