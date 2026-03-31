<?php
namespace Chamilo\Application\Calendar\Architecture\Domain;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarExtensionDataProviderRegistry
{
    public function __construct(protected ArrayCollection $calendarExtensionDataProviders = new ArrayCollection())
    {
    }

    public function addCalendarExtensionDataProvider(
        CalendarExtensionDataProviderInterface $calendarExtensionDataProvider
    ): void
    {
        $this->calendarExtensionDataProviders->set(
            get_class($calendarExtensionDataProvider), $calendarExtensionDataProvider
        );
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface[]
     */
    public function getCalendarExtensionDataProviders(): array
    {
        return $this->calendarExtensionDataProviders->toArray();
    }
}