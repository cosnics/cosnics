<?php
namespace Chamilo\Application\Calendar\Repository;

use Chamilo\Application\Calendar\Storage\DataClass\Availability;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository($dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param boolean $isAvailable
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAvailabilitiesForUser(User $user, $isAvailable = null)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId()));

        if (! is_null($isAvailable))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Availability::class, Availability::PROPERTY_AVAILABILITY),
                new StaticConditionVariable((integer) $isAvailable));
        }

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieves(
            Availability::class,
            new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param boolean $isAvailable
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAvailabilitiesForUserAndCalendarType(User $user, $calendarType, $isAvailable = null)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_CALENDAR_TYPE),
            new StaticConditionVariable($calendarType));

        if (! is_null($isAvailable))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Availability::class, Availability::PROPERTY_AVAILABILITY),
                new StaticConditionVariable((integer) $isAvailable));
        }

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieves(
            Availability::class,
            new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @return \Chamilo\Application\Calendar\Storage\DataClass\Availability
     */
    public function findAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(User $user, $calendarType,
        $calendarIdentifier)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_CALENDAR_TYPE),
            new StaticConditionVariable($calendarType));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_CALENDAR_ID),
            new StaticConditionVariable($calendarIdentifier));
        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            Availability::class,
            new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @param string $calendarType
     * @return boolean
     */
    public function removeAvailabilityByCalendarType($calendarType)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_CALENDAR_TYPE),
            new StaticConditionVariable($calendarType));

        return $this->getDataClassRepository()->deletes(Availability::class, $condition);
    }
}