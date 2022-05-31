<?php
namespace Chamilo\Application\Weblcms\Course\Storage\DataManager\Implementation;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\CaseConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Doctrine implementation of the datamanager
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineExtension
{
    const PARAM_SUBSCRIPTION_STATUS = 'subscription_status';
    const PARAM_SUBSCRIPTION_TYPE = 'subscription_type';

    /**
     * **************************************************************************************************************
     * Implemented Functionality *
     * **************************************************************************************************************
     */

    /**
     * Builds the sql for the users subscribed through (sub) platform groups
     *
     * @param int $course_id
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer[]
     */
    protected function countSubscribedGroupUsers($course_id, $condition)
    {
        $direct_groups_with_tree_values = self::retrieve_direct_subscribed_groups_with_tree_values($course_id);

        if ($direct_groups_with_tree_values->count() > 0)
        {
            $properties = new RetrieveProperties();

            $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_ID));

            $direct_group_conditions = [];
            foreach ($direct_groups_with_tree_values as $group)
            {
                $and_conditions = [];

                $and_conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($group[Group::PROPERTY_LEFT_VALUE])
                );

                $and_conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable($group[Group::PROPERTY_RIGHT_VALUE])
                );

                $direct_group_condition = new AndCondition($and_conditions);
                $direct_group_conditions[] = $direct_group_condition;
            }

            $joins = new Joins();

            $joins->add(
                new Join(
                    GroupRelUser::class, new EqualityCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_ID),
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID)
                    )
                )
            );

            $joins->add(
                new Join(
                    User::class, new EqualityCondition(
                        new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID)
                    )
                )
            );

            $conditions = [];

            $conditions[] = new OrCondition($direct_group_conditions);

            if (isset($condition))
            {
                $conditions[] = $condition;
            }

            $condition = new AndCondition($conditions);

            $parameters = new DataClassDistinctParameters($condition, $properties, $joins);

            return $this->getDataClassDatabase()->distinct(Group::class, $parameters);
        }
    }

    /**
     * @param $course_id
     * @param $condition
     *
     * @return integer[]
     * @throws \Doctrine\DBAL\Exception
     * @throws \ReflectionException
     */
    protected function countSubscribedUsers($course_id, $condition)
    {
        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_ID));

        $joins = new Joins();

        $joins->add(
            new Join(
                User::class, new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID), new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                    )
                )
            )
        );

        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER)
        );

        if (isset($condition))
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        $parameters = new DataClassDistinctParameters($condition, $properties, $joins);

        return $this->getDataClassDatabase()->distinct(CourseEntityRelation::class, $parameters);
    }

    /**
     * Counts all course users of a given course with status and subscription type
     *
     * @param int $course_id
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     *
     */
    public function count_all_course_users($course_id, $condition = null)
    {
        $subscribedUsers = $this->countSubscribedUsers($course_id, $condition);
        $subscribedGroupUsers = $this->countSubscribedGroupUsers($course_id, $condition);

        return count(array_unique(array_merge($subscribedUsers, $subscribedGroupUsers)));
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase
     * @throws \Exception
     */
    protected function getDataClassDatabase()
    {
        return $this->getService('Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase');
    }

    /**
     * @param $serviceName
     *
     * @return object
     * @throws \Exception
     */
    protected function getService($serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    /**
     * Builds the sql for the users subscribed through (sub) platform groups
     *
     * @param int $course_id
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return string[][]
     */
    protected function getSubscribedGroupUsers($course_id, $condition)
    {
        $direct_groups_with_tree_values = self::retrieve_direct_subscribed_groups_with_tree_values($course_id);

        if ($direct_groups_with_tree_values->count() > 0)
        {
            $properties = $this->get_user_properties_for_select();

            $case_condition_variable = new CaseConditionVariable([], self::PARAM_SUBSCRIPTION_STATUS);

            $direct_group_conditions = [];
            foreach ($direct_groups_with_tree_values as $group)
            {
                $and_conditions = [];

                $and_conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($group[Group::PROPERTY_LEFT_VALUE])
                );

                $and_conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable($group[Group::PROPERTY_RIGHT_VALUE])
                );

                $direct_group_condition = new AndCondition($and_conditions);
                $direct_group_conditions[] = $direct_group_condition;

                $case_condition_variable->add(
                    new CaseElementConditionVariable(
                        $group[CourseEntityRelation::PROPERTY_STATUS], $direct_group_condition
                    )
                );
            }

            $properties->add($case_condition_variable);
            $properties->add(new StaticConditionVariable('2 AS ' . self::PARAM_SUBSCRIPTION_TYPE, false));

            $joins = new Joins();

            $joins->add(
                new Join(
                    GroupRelUser::class, new EqualityCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_ID),
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID)
                    )
                )
            );

            $joins->add(
                new Join(
                    User::class, new EqualityCondition(
                        new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID)
                    )
                )
            );

            $conditions = [];

            $conditions[] = new OrCondition($direct_group_conditions);

            if (isset($condition))
            {
                $conditions[] = $condition;
            }

            $condition = new AndCondition($conditions);

            $parameters = new DataClassDistinctParameters($condition, $properties, $joins);

            return $this->getDataClassDatabase()->distinct(Group::class, $parameters);
        }
    }

    /**
     * Builds the sql for the directly subscribed users
     *
     * @param int $course_id
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return string[][]
     */
    protected function getSubscribedUsers($course_id, $condition)
    {
        $properties = $this->get_user_properties_for_select();

        $properties->add(
            new PropertyConditionVariable(
                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_STATUS, self::PARAM_SUBSCRIPTION_STATUS
            )
        );

        $properties->add(new StaticConditionVariable('1 AS ' . self::PARAM_SUBSCRIPTION_TYPE, false));

        $joins = new Joins();

        $joins->add(
            new Join(
                User::class, new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID), new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                    )
                )
            )
        );

        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER)
        );

        if (isset($condition))
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        $parameters = new DataClassDistinctParameters($condition, $properties, $joins);

        return $this->getDataClassDatabase()->distinct(CourseEntityRelation::class, $parameters);
    }

    /**
     * Returns the properties for the user that are used in the select statement
     *
     * @return \Chamilo\Libraries\Storage\Query\RetrieveProperties
     */
    protected function get_user_properties_for_select()
    {
        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_STATUS));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE));

        return $properties;
    }

    /**
     * Retrieves all course users of a given course with status and subscription type
     *
     * @param int $course_id
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param int|null $offset
     * @param int|null $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperties
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function retrieve_all_course_users(
        $course_id, $condition = null, $offset = null, $count = null, $orderProperties = null
    )
    {
        $subscribedUsers = $this->getSubscribedUsers($course_id, $condition);
        $subscribedGroupUsers = $this->getSubscribedGroupUsers($course_id, $condition);

        $users = [];

        foreach ($subscribedUsers as $subscribedUser)
        {
            $users[$subscribedUser[User::PROPERTY_ID]] = $subscribedUser;
        }

        foreach ($subscribedGroupUsers as $subscribedGroupUser)
        {
            if (array_key_exists($subscribedGroupUser[User::PROPERTY_ID], $users))
            {
                $existingUser = $users[User::PROPERTY_ID];

                if ($existingUser[User::PROPERTY_STATUS] > $subscribedGroupUser[User::PROPERTY_STATUS])
                {
                    $users[$subscribedGroupUser[User::PROPERTY_ID]] = $subscribedGroupUser;
                }
            }
            else
            {
                $users[$subscribedGroupUser[User::PROPERTY_ID]] = $subscribedGroupUser;
            }
        }

        usort(
            $users, function ($user1, $user2) use ($orderProperties) {

            $orderProperty = $orderProperties->getFirst();

            return strcmp(
                $user1[$orderProperty->getConditionVariable()->getPropertyName()],
                $user2[$orderProperty->getConditionVariable()->getPropertyName()]
            );
        }
        );

        return new ArrayCollection(array_slice($users, $offset, $count));
    }

    /**
     * Retrieves the direct subscribed groups joined with the group table to support the tree values
     *
     * @param $course_id
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function retrieve_direct_subscribed_groups_with_tree_values($course_id)
    {
        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE));
        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE));

        $properties->add(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_STATUS)
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                Group::class, new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                    )
                )
            )
        );

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
        );
        $condition = new AndCondition($conditions);

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, null, $joins);

        return new ArrayCollection(
            $this->getDataClassDatabase()->records(CourseEntityRelation::class, $parameters)
        );
    }
}
