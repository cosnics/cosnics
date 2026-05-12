<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderRegistry;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Core\User\Storage\Entity\User;

/**
 * @package Chamilo\Application\Calendar\Implementation\Libraries
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarDataProvider
{
    public function __construct(
        protected CalendarExtensionDataProviderRegistry $calendarExtensionDataProviderRegistry,
        protected VisibilityRepository $visibilityRepository
    )
    {
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     */
    public function getEvents(User $user, int $startTime, int $endTime): array
    {
        $events = [];

        foreach (
            $this->calendarExtensionDataProviderRegistry->getCalendarExtensionDataProviders() as
            $calendarExtensionDataProvider
        ) {
            $extensionEvents = $calendarExtensionDataProvider->getEvents($user, $startTime, $endTime);

            $events = array_merge($events, $extensionEvents);
        }

        return $events;
    }

    /**
     * @return string[]
     */
    public function getSourceNames(): array
    {
        $sourceNames = [];

        foreach (
            $this->calendarExtensionDataProviderRegistry->getCalendarExtensionDataProviders() as
            $calendarExtensionDataProvider
        ) {
            $sourceNames[] = $calendarExtensionDataProvider->getName();
        }

        sort($sourceNames);

        return $sourceNames;
    }

    /**
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Visibility[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getVisibilities($userIdentifier): array
    {
        $visibilities = $this->visibilityRepository->retrieveVisibilitiesByUserIdentifier(
            $userIdentifier
        );

        $indexedVisibilities = [];

        foreach ($visibilities as $visibility) {
            $indexedVisibilities[$visibility->getSource()] = $visibility;
        }

        return $indexedVisibilities;
    }
}