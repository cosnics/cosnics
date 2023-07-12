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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AvailabilityRepository
{

    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param bool $isAvailable
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Calendar\Storage\DataClass\Availability>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findAvailabilitiesForUser(User $user, ?bool $isAvailable = null): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        if (!is_null($isAvailable))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Availability::class, Availability::PROPERTY_AVAILABILITY),
                new StaticConditionVariable((integer) $isAvailable)
            );
        }

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieves(
            Availability::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param ?bool $isAvailable
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Calendar\Storage\DataClass\Availability>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findAvailabilitiesForUserAndCalendarType(User $user, string $calendarType, ?bool $isAvailable = null
    ): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_CALENDAR_TYPE),
            new StaticConditionVariable($calendarType)
        );

        if (!is_null($isAvailable))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Availability::class, Availability::PROPERTY_AVAILABILITY),
                new StaticConditionVariable((integer) $isAvailable)
            );
        }

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieves(
            Availability::class, new DataClassRetrievesParameters($condition)
        );
    }

    public function findAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(
        User $user, string $calendarType, string $calendarIdentifier
    ): ?Availability
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_CALENDAR_TYPE),
            new StaticConditionVariable($calendarType)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_CALENDAR_ID),
            new StaticConditionVariable($calendarIdentifier)
        );
        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            Availability::class, new DataClassRetrieveParameters($condition)
        );
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    public function removeAvailabilityByCalendarType(string $calendarType): bool
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Availability::class, Availability::PROPERTY_CALENDAR_TYPE),
            new StaticConditionVariable($calendarType)
        );

        return $this->getDataClassRepository()->deletes(Availability::class, $condition);
    }
}