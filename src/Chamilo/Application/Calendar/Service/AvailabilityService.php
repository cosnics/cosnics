<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Storage\DataClass\Availability;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ActionResult;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use ReflectionClass;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityService
{
    public const PROPERTY_AVAILABLE = 'available';
    public const PROPERTY_CALENDAR = 'calendar';
    public const PROPERTY_COLOUR = 'colour';

    protected RegistrationConsulter $registrationConsulter;

    private AvailabilityRepository $availabilityRepository;

    public function __construct(
        AvailabilityRepository $availabilityRepository, RegistrationConsulter $registrationConsulter
    )
    {
        $this->availabilityRepository = $availabilityRepository;
        $this->registrationConsulter = $registrationConsulter;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageLastInsertedIdentifierException
     */
    public function createAvailability(Availability $availability): bool
    {
        return $this->getAvailabilityRepository()->createAvailability($availability);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageLastInsertedIdentifierException
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function deleteAvailabilityByCalendarType(string $calendarType): bool
    {
        return $this->getAvailabilityRepository()->removeAvailabilityByCalendarType($calendarType);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Calendar\Storage\DataClass\Availability>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function getActiveAvailabilitiesForUserAndCalendarType(User $user, string $calendarType): ArrayCollection
    {
        return $this->getAvailabilitiesForUserAndCalendarType($user, $calendarType, true);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Calendar\Storage\DataClass\Availability>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function getAvailabilitiesForUser(User $user, ?bool $isAvailable = null): ArrayCollection
    {
        return $this->getAvailabilityRepository()->findAvailabilitiesForUser($user, $isAvailable);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Calendar\Storage\DataClass\Availability>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function getAvailabilitiesForUserAndCalendarType(User $user, string $calendarType, ?bool $isAvailable = null
    ): ArrayCollection
    {
        return $this->getAvailabilityRepository()->findAvailabilitiesForUserAndCalendarType(
            $user, $calendarType, $isAvailable
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException
     */
    public function getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
        User $user, string $calendarType, string $calendarIdentifier
    ): Availability
    {
        return $this->getAvailabilityRepository()->findAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $user, $calendarType, $calendarIdentifier
        );
    }

    public function getAvailabilityRepository(): AvailabilityRepository
    {
        return $this->availabilityRepository;
    }

    /**
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getAvailableCalendars(User $user): array
    {
        $availableCalendars = [];

        $registrations = $this->getRegistrationConsulter()->getIntegrationRegistrations(
            Manager::CONTEXT
        );

        foreach ($registrations as $registration)
        {
            $context = $registration[Registration::PROPERTY_CONTEXT];
            $class_name = $context . '\Service\CalendarEventDataProvider';

            if (class_exists($class_name))
            {
                $reflectionClass = new ReflectionClass($class_name);
                if ($reflectionClass->isAbstract())
                {
                    continue;
                }

                $package = ClassnameUtilities::getInstance()->getNamespaceParent($context, 4);
                $implementor = new $class_name();
                $availableCalendars[$package] = $implementor->getCalendars($user);
            }
        }

        return $availableCalendars;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Calendar\Storage\DataClass\Availability>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function getInactiveAvailabilitiesForUserAndCalendarType(User $user, string $calendarType): ArrayCollection
    {
        return $this->getAvailabilitiesForUserAndCalendarType($user, $calendarType, false);
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     *
     * @return bool
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
        User $user, string $calendarType, string $calendarIdentifier
    ): bool
    {
        try
        {
            $availability = $this->getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
                $user, $calendarType, $calendarIdentifier
            );

            return $availability->getAvailability() == 1;
        }
        catch (StorageNoResultException)
        {
            return false;
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string[][][] $calendarAvailabilityTypes
     *
     * @return \Chamilo\Libraries\Architecture\ActionResult
     * @throws \Exception
     */
    public function setAvailabilities(User $user, array $calendarAvailabilityTypes = []): ActionResult
    {
        $failedActions = 0;

        foreach ($calendarAvailabilityTypes as $calendarType => $calendarAvailabilities)
        {
            foreach ($calendarAvailabilities as $calendarIdentifier => $settings)
            {
                try
                {
                    $this->setAvailability(
                        $user, $calendarType, $calendarIdentifier, (boolean) $settings[self::PROPERTY_AVAILABLE],
                        $settings[self::PROPERTY_COLOUR]
                    );
                }
                catch (Exception)
                {
                    $failedActions ++;
                }
            }
        }

        return new ActionResult(
            count($calendarAvailabilityTypes), $failedActions, __NAMESPACE__, __FUNCTION__, 'Availability'
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param bool $isAvailable
     * @param ?string $colour
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function setAvailability(
        User $user, string $calendarType, string $calendarIdentifier, bool $isAvailable = true, ?string $colour = null
    ): Availability
    {
        try
        {
            $availability = $this->getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
                $user, $calendarType, $calendarIdentifier
            );

            return $this->updateAvailabilityFromParameters(
                $availability, $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
            );
        }
        catch (StorageNoResultException)
        {
            return $this->createAvailabilityFromParameters(
                $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
            );
        }
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     */
    public function updateAvailability(Availability $availability): bool
    {
        return $this->getAvailabilityRepository()->updateAvailability($availability);
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
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