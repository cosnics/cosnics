<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\Recurrence\RecurrenceCalculator;

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

    /**
     * @param string[] $displayParameters ;
     */
    public function __construct(User $dataUser, array $displayParameters)
    {
        $this->dataUser = $dataUser;
        $this->displayParameters = $displayParameters;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    abstract public function aggregateEvents(?int $startTime = null, ?int $endTime = null): array;

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
    public function getEvents(?int $startTime = null, ?int $endTime = null, bool $calculateRecurrence = false): array
    {
        $cacheIdentifier = md5(serialize(array($startTime, $endTime, $calculateRecurrence)));

        if (!isset($this->events[$cacheIdentifier]))
        {
            $events = $this->aggregateEvents($startTime, $endTime);

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
    public function getEventsInPeriod(int $startTime, int $endTime, bool $calculateRecurrence = true): array
    {
        return $this->getEvents($startTime, $endTime, $calculateRecurrence);
    }
}