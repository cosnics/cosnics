<?php
namespace Chamilo\Application\Calendar\Service;

use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Application\Calendar\Storage\DataClass\Availability;
use Chamilo\Libraries\Architecture\ActionResult;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

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
     * @param boolean $isAvailable
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
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
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getAvailabilitiesForUserAndCalendarType(User $user, $calendarType, $isAvailable = null)
    {
        return $this->getAvailabilityRepository()->findAvailabilitiesForUserAndCalendarType(
            $user,
            $calendarType,
            $isAvailable);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getActiveAvailabilitiesForUserAndCalendarType(User $user, $calendarType)
    {
        return $this->getAvailabilitiesForUserAndCalendarType($user, $calendarType, true);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getInactiveAvailabilitiesForUser(User $user, $calendarType)
    {
        return $this->getAvailabilitiesForUserAndCalendarType($user, $calendarType, false);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param boolean $isAvailable
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     */
    public function createAvailability(User $user, $calendarType, $calendarIdentifier, $isAvailable = true)
    {
        $availability = new Availability();
        $this->setAvailabilityProperties($availability, $user, $calendarType, $calendarIdentifier, $isAvailable);

        if (! $availability->create())
        {
            return false;
        }

        return $availability;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Storage\DataClass\Availability $availability
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param boolean $isAvailable
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     */
    public function updateAvailability(Availability $availability, User $user, $calendarType, $calendarIdentifier,
        $isAvailable = true)
    {
        $this->setAvailabilityProperties($availability, $user, $calendarType, $calendarIdentifier, $isAvailable);

        if (! $availability->update())
        {
            return false;
        }

        return $availability;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Storage\DataClass\Availability $availability
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param boolean $isAvailable
     */
    private function setAvailabilityProperties(Availability $availability, User $user, $calendarType,
        $calendarIdentifier, $isAvailable)
    {
        $availability->setUserId($user->getId());
        $availability->setCalendarType($calendarType);
        $availability->setCalendarId($calendarIdentifier);
        $availability->setAvailability($isAvailable);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @param boolean $isAvailable
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     */
    public function setAvailability(User $user, $calendarType, $calendarIdentifier, $isAvailable = true)
    {
        $availability = $this->getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $user,
            $calendarType,
            $calendarIdentifier);

        if ($availability instanceof Availability)
        {
            return $this->updateAvailability($availability, $user, $calendarType, $calendarIdentifier, $isAvailable);
        }
        else
        {
            return $this->createAvailability($user, $calendarType, $calendarIdentifier, $isAvailable);
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $calendarAvailabilityTypes
     * @return \Chamilo\Libraries\Architecture\ActionResult
     */
    public function setAvailabilities(User $user, $calendarAvailabilityTypes = array())
    {
        $failedActions = 0;

        foreach ($calendarAvailabilityTypes as $calendarType => $calendarAvailabilities)
        {
            foreach ($calendarAvailabilities as $calendarIdentifier => $isAvailable)
            {
                if (! $this->setAvailability($user, $calendarType, $calendarIdentifier, (boolean) $isAvailable))
                {
                    $failedActions ++;
                }
            }
        }

        return new ActionResult(
            count($calendarAvailabilityTypes),
            $failedActions,
            __METHOD__,
            Availability :: class_name(false));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     */
    public function getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(User $user, $calendarType,
        $calendarIdentifier)
    {
        return $this->getAvailabilityRepository()->findAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $user,
            $calendarType,
            $calendarIdentifier);
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getAvailableCalendars()
    {
        $availableCalendars = array();

        $registrations = \Chamilo\Configuration\Storage\DataManager :: get_integrating_contexts(
            \Chamilo\Application\Calendar\Manager :: context());

        foreach ($registrations as $registration)
        {
            $context = $registration->get_context();
            $class_name = $context . '\Manager';

            if (class_exists($class_name))
            {
                $package = ClassnameUtilities :: getInstance()->getNamespaceParent($context, 4);
                $implementor = new $class_name();
                $availableCalendars[$package] = $implementor->getCalendars();
            }
        }

        return $availableCalendars;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @return boolean
     */
    public function isAvailableForUserAndCalendarTypeAndCalendarIdentifier(User $user, $calendarType,
        $calendarIdentifier)
    {
        $availability = $this->getAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
            $user,
            $calendarType,
            $calendarIdentifier);

        return ! $availability instanceof Availability || $availability->getAvailability() == 1;
    }
}