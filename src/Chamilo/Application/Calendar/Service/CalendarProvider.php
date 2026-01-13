<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\ActionsProviderInterface;
use Chamilo\Application\Calendar\Architecture\CalendarDataProviderInterface;

/**
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarProvider
{
    /**
     * @var \Chamilo\Application\Calendar\Architecture\ActionsProviderInterface[]
     */
    protected array $actionsProviders;

    /**
     * @var \Chamilo\Application\Calendar\Architecture\CalendarDataProviderInterface[]
     */
    protected array $calendarDataProviders;

    public function __construct()
    {
        $this->calendarDataProviders = [];
        $this->actionsProviders = [];
    }

    public function addActionsProvider(ActionsProviderInterface $actionsProvider): void
    {
        $this->actionsProviders[] = $actionsProvider;
    }

    public function addCalendarDataProvider(CalendarDataProviderInterface $calendarDataProvider): void
    {
        $this->calendarDataProviders[] = $calendarDataProvider;
    }

    public function getActionsProviders(): array
    {
        return $this->actionsProviders;
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\CalendarDataProviderInterface[]
     */
    public function getCalendarDataProviders(): array
    {
        return $this->calendarDataProviders;
    }
}