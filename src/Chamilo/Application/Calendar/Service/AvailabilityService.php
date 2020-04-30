<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Storage\DataClass\Availability;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ActionResult;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use ReflectionClass;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityService
{
    const PROPERTY_AVAILABLE = 'available';

    const PROPERTY_CALENDAR = 'calendar';

    const PROPERTY_COLOUR = 'colour';

    /**
     *
     * @var \Chamilo\Application\Calendar\Repository\AvailabilityRepository
     */
    private $availabilityRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Repository\AvailabilityRepository $availabilityRepository
     */
    public function __construct(AvailabilityRepository $availabilityRepository)
    {
        $this->availabilityRepository = $availabilityRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param boolean $isAvailable
     * @param string $colour
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     */
    public function createAvailability(
        User $user, $calendarType, $calendarIdentifier, $isAvailable = true, $colour = null
    )
    {
        $availability = new Availability();
        $this->setAvailabilityProperties(
            $availability, $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
        );

        if (!$availability->create())
        {
            return false;
        }

        return $availability;
    }

    /**
     *
     * @param string $calendarType
     *
     * @return boolean
     */
    public function deleteAvailabilityByCalendarType($calendarType)
    {
        return $this->getAvailabilityRepository()->removeAvailabilityByCalendarType($calendarType);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getActiveAvailabilitiesForUserAndCalendarType(User $user, $calendarType)
    {
        return $this->getAvailabilitiesForUserAndCalendarType($user, $calendarType, true);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param boolean $isAvailable
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getAvailabilitiesForUser(User $user, $isAvailable = null)
    {
        return $this->getAvailabilityRepository()->findAvailabilitiesForUser($user, $isAvailable);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param boolean $isAvailable
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getAvailabilitiesForUserAndCalendarType(User $user, $calendarType, $isAvailable = null)
    {
        return $this->getAvailabilityRepository()->findAvailabilitiesForUserAndCalendarType(
            $user, $calendarType, $isAvailable
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     */
    public function getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
        User $user, $calendarType, $calendarIdentifier
    )
    {
        return $this->getAvailabilityRepository()->findAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $user, $calendarType, $calendarIdentifier
        );
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Repository\AvailabilityRepository
     */
    public function getAvailabilityRepository()
    {
        return $this->availabilityRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Repository\AvailabilityRepository $availabilityRepository
     */
    public function setAvailabilityRepository(AvailabilityRepository $availabilityRepository)
    {
        $this->availabilityRepository = $availabilityRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getAvailableCalendars(User $user)
    {
        $availableCalendars = array();

        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            Manager::package()
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
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getInactiveAvailabilitiesForUserAndCalendarType(User $user, $calendarType)
    {
        return $this->getAvailabilitiesForUserAndCalendarType($user, $calendarType, false);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     *
     * @return boolean
     */
    public function isAvailableForUserAndCalendarTypeAndCalendarIdentifier(
        User $user, $calendarType, $calendarIdentifier
    )
    {
        $availability = $this->getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $user, $calendarType, $calendarIdentifier
        );

        return !$availability instanceof Availability || $availability->getAvailability() == 1;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $calendarAvailabilityTypes
     *
     * @return \Chamilo\Libraries\Architecture\ActionResult
     */
    public function setAvailabilities(User $user, $calendarAvailabilityTypes = array())
    {
        $failedActions = 0;

        foreach ($calendarAvailabilityTypes as $calendarType => $calendarAvailabilities)
        {
            foreach ($calendarAvailabilities as $calendarIdentifier => $settings)
            {
                if (!$this->setAvailability(
                    $user, $calendarType, $calendarIdentifier, (boolean) $settings[self::PROPERTY_AVAILABLE],
                    $settings[self::PROPERTY_COLOUR]
                ))
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
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param boolean $isAvailable
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     */
    public function setAvailability(User $user, $calendarType, $calendarIdentifier, $isAvailable = true, $colour = null)
    {
        $availability = $this->getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $user, $calendarType, $calendarIdentifier
        );

        if ($availability instanceof Availability)
        {
            return $this->updateAvailability(
                $availability, $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
            );
        }
        else
        {
            return $this->createAvailability($user, $calendarType, $calendarIdentifier, $isAvailable, $colour);
        }
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Storage\DataClass\Availability $availability
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param boolean $isAvailable
     * @param string $colour
     */
    private function setAvailabilityProperties(
        Availability $availability, User $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
    )
    {
        $availability->setUserId($user->getId());
        $availability->setCalendarType($calendarType);
        $availability->setCalendarId($calendarIdentifier);
        $availability->setAvailability($isAvailable);
        $availability->setColour($colour);
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Storage\DataClass\Availability $availability
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param boolean $isAvailable
     * @param string $colour
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     */
    public function updateAvailability(
        Availability $availability, User $user, $calendarType, $calendarIdentifier, $isAvailable = true, $colour = null
    )
    {
        $this->setAvailabilityProperties(
            $availability, $user, $calendarType, $calendarIdentifier, $isAvailable, $colour
        );

        if (!$availability->update())
        {
            return false;
        }

        return $availability;
    }
}