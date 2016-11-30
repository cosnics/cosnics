<?php
namespace Chamilo\Application\Calendar\Repository;

use Chamilo\Application\Calendar\Storage\DataClass\Availability;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param boolean $isAvailable
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAvailabilitiesForUser(User $user, $isAvailable = null)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class_name(), Availability::PROPERTY_USER_ID), 
            new StaticConditionVariable($user->getId()));
        
        if (! is_null($isAvailable))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Availability::class_name(), Availability::PROPERTY_AVAILABILITY), 
                new StaticConditionVariable((integer) $isAvailable));
        }
        
        $condition = new AndCondition($conditions);
        
        return DataManager::retrieves(Availability::class_name(), new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param boolean $isAvailable
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAvailabilitiesForUserAndCalendarType(User $user, $calendarType, $isAvailable = null)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class_name(), Availability::PROPERTY_USER_ID), 
            new StaticConditionVariable($user->getId()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class_name(), Availability::PROPERTY_CALENDAR_TYPE), 
            new StaticConditionVariable($calendarType));
        
        if (! is_null($isAvailable))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Availability::class_name(), Availability::PROPERTY_AVAILABILITY), 
                new StaticConditionVariable((integer) $isAvailable));
        }
        
        $condition = new AndCondition($conditions);
        
        return DataManager::retrieves(Availability::class_name(), new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarType
     * @param string $calendarIdentifier
     * @return \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Availability
     */
    public function findAvailabilityByUserAndCalendarTypeAndCalendarIdentifier(User $user, $calendarType, 
        $calendarIdentifier)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class_name(), Availability::PROPERTY_USER_ID), 
            new StaticConditionVariable($user->getId()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class_name(), Availability::PROPERTY_CALENDAR_TYPE), 
            new StaticConditionVariable($calendarType));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Availability::class_name(), Availability::PROPERTY_CALENDAR_ID), 
            new StaticConditionVariable($calendarIdentifier));
        $condition = new AndCondition($conditions);
        
        return DataManager::retrieve(Availability::class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @param string $calendarType
     * @return boolean
     */
    public function removeAvailabilityByCalendarType($calendarType)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Availability::class_name(), Availability::PROPERTY_CALENDAR_TYPE), 
            new StaticConditionVariable($calendarType));
        
        return DataManager::deletes(Availability::class_name(), $condition);
    }
}