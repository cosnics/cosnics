<?php
namespace Chamilo\Application\Calendar\Architecture\Domain;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionActionProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Calendar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarExtensionActionProviderRegistry extends ArrayCollection
{
    public function addCalendarExtenstionActionProvider(
        CalendarExtensionActionProviderInterface $calendarExtensionActionProvider
    ): void
    {
        $this->set(get_class($calendarExtensionActionProvider), $calendarExtensionActionProvider);
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionActionProviderInterface[]
     */
    public function getCalendarExtenstionActionProviders(): array
    {
        return $this->toArray();
    }
}