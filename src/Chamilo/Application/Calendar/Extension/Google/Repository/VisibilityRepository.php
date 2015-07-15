<?php
namespace Chamilo\Application\Calendar\Extension\Google\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VisibilityRepository
{

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findUserVisibilities(User $user)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Visibility :: class_name(), Visibility :: PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId()));

        return DataManager :: retrieves(Visibility :: class_name(), new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $calendarIdentifier
     * @return \Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility
     */
    public function findVisibilityByUserIdentifierAndCalendarIdentifier(User $user, $calendarIdentifier)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility :: class_name(), Visibility :: PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Visibility :: class_name(), Visibility :: PROPERTY_CALENDAR_ID),
            new StaticConditionVariable($calendarIdentifier));
        $condition = new AndCondition($conditions);

        return DataManager :: retrieve(Visibility :: class_name(), new DataClassRetrieveParameters($condition));
    }
}