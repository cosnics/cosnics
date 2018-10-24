<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Provides the calendar events for the personal calendar extension
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CalendarEventDataProviderRepository
{

    /**
     * Returns the base data class retrieves parameters to retrieve own publications for a given user
     *
     * @param User $user
     *
     * @return RecordRetrievesParameters
     */
    public function getPublicationsRecordRetrievesParameters(User $user)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER),
            new StaticConditionVariable($user->getId()));

        return new RecordRetrievesParameters(
            new DataClassProperties(array(new PropertiesConditionVariable(Publication::class))),
            $condition);
    }

    /**
     * Returns the base data class retrieves parameters to retrieve shared publications for a given user
     *
     * @param User $user
     *
     * @return RecordRetrievesParameters
     */
    public function getSharedPublicationsRecordRetrievesParameters(User $user)
    {
        $user_groups = $user->get_groups(true);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser::class_name(), PublicationUser::PROPERTY_USER),
            new StaticConditionVariable($user->getId()));

        if (count($user_groups) > 0)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(PublicationGroup::class_name(), PublicationGroup::PROPERTY_GROUP_ID),
                $user_groups);
        }

        $share_condition = new OrCondition($conditions);

        $publisher_condition = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER),
                new StaticConditionVariable($user->getId())));

        $condition = new AndCondition(array($share_condition, $publisher_condition));

        $joins = array();
        $joins[] = new Join(
            PublicationUser::class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID),
                new PropertyConditionVariable(PublicationUser::class_name(), PublicationUser::PROPERTY_PUBLICATION)),
            Join::TYPE_LEFT);
        $joins[] = new Join(
            PublicationGroup::class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID),
                new PropertyConditionVariable(PublicationGroup::class_name(), PublicationGroup::PROPERTY_PUBLICATION)),
            Join::TYPE_LEFT);

        return new RecordRetrievesParameters(
            new DataClassProperties(array(new PropertiesConditionVariable(Publication::class))),
            $condition,
            null,
            null,
            array(),
            new Joins($joins));
    }
}