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
    public const SOURCE_TYPE_BOTH = 3;
    public const SOURCE_TYPE_EXTERNAL = 2;
    public const SOURCE_TYPE_INTERNAL = 1;

    private User $dataUser;

    /**
     * @var string[]
     */
    private array $displayParameters;

    /**
     * @var \Chamilo\Libraries\Calendar\Event\Event[][]
     */
    private array $events;

    private User $viewingUser;

    /**
     * @param string[] $displayParameters ;
     */
    public function __construct(User $dataUser, User $viewingUser, array $displayParameters)
    {
        $this->dataUser = $dataUser;
        $this->viewingUser = $viewingUser;
        $this->displayParameters = $displayParameters;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    abstract public function aggregateEvents(int $sourceType, int $startTime, int $endTime): array;

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEvents(): array
    {
        return $this->getEvents(self::SOURCE_TYPE_BOTH);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEventsInPeriod(int $startTime, int $endTime, bool $calculateRecurrence = true): array
    {
        return $this->getEvents(self::SOURCE_TYPE_BOTH, $startTime, $endTime, $calculateRecurrence);
    }

    public function getDataUser(): User
    {
        return $this->dataUser;
    }

    public function setDataUser(User $dataUser)
    {
        $this->dataUser = $dataUser;
    }

    /**
     * @return string[]
     */
    public function getDisplayParameters(): array
    {
        return $this->displayParameters;
    }

    /**
     * @param string[] $displayParameters
     */
    public function setDisplayParameters(array $displayParameters)
    {
        $this->displayParameters = $displayParameters;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     * @throws \Sabre\VObject\InvalidDataException
     */
    private function getEvents(
        int $sourceType, ?int $startTime = null, ?int $endTime = null, bool $calculateRecurrence = false
    ): array
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
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     * @throws \Sabre\VObject\InvalidDataException
     */
    public function getExternalEvents(): array
    {
        return $this->getEvents(self::SOURCE_TYPE_EXTERNAL);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     * @throws \Sabre\VObject\InvalidDataException
     */
    public function getExternalEventsInPeriod(int $startTime, int $endTime, bool $calculateRecurrence = true): array
    {
        return $this->getEvents(self::SOURCE_TYPE_EXTERNAL, $startTime, $endTime, $calculateRecurrence);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     * @throws \Sabre\VObject\InvalidDataException
     */
    public function getInternalEvents(): array
    {
        return $this->getEvents(self::SOURCE_TYPE_INTERNAL);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     * @throws \Sabre\VObject\InvalidDataException
     */
    public function getInternalEventsInPeriod(int $startTime, int $endTime, bool $calculateRecurrence = true): array
    {
        return $this->getEvents(self::SOURCE_TYPE_INTERNAL, $startTime, $endTime, $calculateRecurrence);
    }

    public function getViewingUser(): User
    {
        return $this->viewingUser;
    }

    public function setViewingUser(User $viewingUser)
    {
        $this->viewingUser = $viewingUser;
    }

    public function isExternalSource(int $source): bool
    {
        return $this->matchesRequestedSource(self::SOURCE_TYPE_EXTERNAL, $source);
    }

    public function isInternalSource(int $source): bool
    {
        return $this->matchesRequestedSource(self::SOURCE_TYPE_INTERNAL, $source);
    }

    public function matchesRequestedSource(int $requestedSource, int $implementationSource): bool
    {
        return (boolean) ($requestedSource & $implementationSource);
    }
}