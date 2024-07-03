<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\StorageParameters;

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
     * @return StorageParameters
     */
    public function getPublicationsParameters(User $user)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLISHER),
            new StaticConditionVariable($user->getId())
        );

        return new StorageParameters(
            condition: $condition, retrieveProperties: new RetrieveProperties(
            [new PropertiesConditionVariable(Publication::class)]
        )
        );
    }

    /**
     * Returns the base data class retrieves parameters to retrieve shared publications for a given user
     *
     * @param User $user
     *
     * @return StorageParameters
     */
    public function getSharedPublicationsParameters(User $user)
    {
        $user_groups = $user->get_groups(true);

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser::class, PublicationUser::PROPERTY_USER),
            new StaticConditionVariable($user->getId())
        );

        if (count($user_groups) > 0)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(PublicationGroup::class, PublicationGroup::PROPERTY_GROUP_ID),
                $user_groups
            );
        }

        $share_condition = new OrCondition($conditions);

        $publisher_condition = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLISHER),
                new StaticConditionVariable($user->getId())
            )
        );

        $condition = new AndCondition([$share_condition, $publisher_condition]);

        $joins = [];
        $joins[] = new Join(
            PublicationUser::class, new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_ID),
            new PropertyConditionVariable(PublicationUser::class, PublicationUser::PROPERTY_PUBLICATION)
        ), Join::TYPE_LEFT
        );
        $joins[] = new Join(
            PublicationGroup::class, new EqualityCondition(
            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_ID),
            new PropertyConditionVariable(PublicationGroup::class, PublicationGroup::PROPERTY_PUBLICATION)
        ), Join::TYPE_LEFT
        );

        return new StorageParameters(
            condition: $condition, joins: new Joins($joins), retrieveProperties: new RetrieveProperties(
            [new PropertiesConditionVariable(Publication::class)]
        )
        );
    }
}