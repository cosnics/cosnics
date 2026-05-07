<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderRegistry;
use Chamilo\Application\Calendar\Storage\DataClass\Availability;
use Chamilo\Application\Calendar\Storage\Repository\AvailabilityRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ActionResult;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @package Chamilo\Application\Calendar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityService
{
    public function __construct(
        protected AvailabilityRepository $availabilityRepository,
        protected CalendarExtensionDataProviderRegistry $calendarProvider
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createAvailability(Availability $availability): void
    {
        $this->availabilityRepository->createAvailability($availability);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Exception
     */
    public function createAvailabilityFromParameters(
        User $user, string $calendarType, string $calendarIdentifier, bool $isAvailable = true, ?string $colour = null
    ): Availability
    {
        $availability = new Availability();
        $this->setAvailabilityProperties(
            $availability, $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
        );

        $this->createAvailability($availability);

        return $availability;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteAvailabilityByCalendarType(string $calendarType): void
    {
        $this->availabilityRepository->removeAvailabilityByCalendarType($calendarType);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Calendar\Storage\DataClass\Availability>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getAvailabilitiesForUser(User $user, ?bool $isAvailable = null): ArrayCollection
    {
        return $this->availabilityRepository->findAvailabilitiesForUser($user, $isAvailable);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Calendar\Storage\DataClass\Availability>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getAvailabilitiesForUserAndCalendarType(User $user, string $calendarType, ?bool $isAvailable = null
    ): ArrayCollection
    {
        return $this->availabilityRepository->findAvailabilitiesForUserAndCalendarType(
            $user, $calendarType, $isAvailable
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
        User $user, string $calendarType, string $calendarIdentifier
    ): Availability
    {
        return $this->availabilityRepository->findAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $user, $calendarType, $calendarIdentifier
        );
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar[][]
     */
    public function getAvailableCalendars(User $user): array
    {
        $availableCalendars = [];

        foreach ($this->calendarProvider->getCalendarExtensionDataProviders() as $calendarDataProvider) {
            $calendars = $calendarDataProvider->getCalendars($user);

            if (count($calendars) > 0) {
                $availableCalendars[$calendars[0]->type] = $calendars;
            }
        }

        return $availableCalendars;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param bool $isAvailable
     * @param ?string $colour
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function saveAvailability(
        User $user, string $calendarType, string $calendarIdentifier, bool $isAvailable = true, ?string $colour = null
    ): Availability
    {
        try {
            $availability = $this->getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
                $user, $calendarType, $calendarIdentifier
            );

            return $this->updateAvailabilityFromParameters(
                $availability, $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
            );
        }
        catch (StorageNoResultException) {
            return $this->createAvailabilityFromParameters(
                $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function setAvailabilitiesFromParameters(User $user, array $availabilityData = []): ActionResult
    {
        $failedActions = 0;
        $numberOfCalendars = 0;

        $calendars = $this->getAvailableCalendars($user);

        foreach ($calendars as $calendarType => $calendarTypeCalendars) {
            foreach ($calendarTypeCalendars as $calendarTypeCalendar) {
                try {
                    $numberOfCalendars ++;
                    $this->saveAvailability(
                        $user, $calendarType, $calendarTypeCalendar->identifier,
                        (boolean) $availabilityData[$calendarTypeCalendar->getUniqueIdentifier()]
                    );
                }
                catch (Exception) {
                    $failedActions ++;
                }
            }
        }

        return new ActionResult(
            $numberOfCalendars, $failedActions, __NAMESPACE__, __FUNCTION__, 'Availability'
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Storage\DataClass\Availability $availability
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param bool $isAvailable
     * @param ?string $colour
     */
    private function setAvailabilityProperties(
        Availability $availability, User $user, string $calendarType, string $calendarIdentifier,
        bool $isAvailable = true, ?string $colour = null
    ): void
    {
        $availability->setUserId($user->getId());
        $availability->setCalendarType($calendarType);
        $availability->setCalendarId($calendarIdentifier);
        $availability->setAvailability($isAvailable);
        $availability->setColour($colour);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateAvailability(Availability $availability): void
    {
        $this->availabilityRepository->updateAvailability($availability);
    }

    /**
     * @param \Chamilo\Application\Calendar\Storage\DataClass\Availability $availability
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param bool $isAvailable
     * @param ?string $colour
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateAvailabilityFromParameters(
        Availability $availability, User $user, string $calendarType, string $calendarIdentifier,
        bool $isAvailable = true, ?string $colour = null
    ): Availability
    {
        $this->setAvailabilityProperties(
            $availability, $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
        );

        $this->updateAvailability($availability);

        return $availability;
    }
}