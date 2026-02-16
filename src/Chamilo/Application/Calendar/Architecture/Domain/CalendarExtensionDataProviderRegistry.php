<?php
namespace Chamilo\Application\Calendar\Architecture\Domain;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarExtensionDataProviderRegistry extends ArrayCollection
{
    public function addCalendarExtensionDataProvider(
        CalendarExtensionDataProviderInterface $calendarExtensionDataProvider
    ): void
    {
        $this->set(get_class($calendarExtensionDataProvider), $calendarExtensionDataProvider);
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface[]
     */
    public function getCalendarExtensionDataProviders(): array
    {
        return $this->toArray();
    }
}