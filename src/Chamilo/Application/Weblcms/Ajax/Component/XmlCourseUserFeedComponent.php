<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

class XmlCourseUserFeedComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{

    public function run()
    {
        $user_result_set = $this->retrieve_users();
        $users = array();

        if ($user_result_set)
        {
            while ($user = $user_result_set->next_result())
            {
                $users[] = $user;
            }
        }

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

        $this->dump_tree($users);

        echo '</tree>';
    }

    function dump_tree($users)
    {
        if ($this->contains_results($users))
        {
            echo '<node id="user" classes="category unlinked" title="Users">', "\n";
            foreach ($users as $user)
            {
                echo '<leaf id="user_' . $user->get_id() . '" classes="' . 'type type_user' . '" title="';
                echo htmlentities($user->get_username()) . '" description="';
                echo htmlentities($user->get_fullname()) . '"/>' . "\n";
            }
            echo '</node>', "\n";
        }
    }

    function contains_results($objects)
    {
        if (count($objects))
        {
            return true;
        }
        return false;
    }

    function retrieve_users()
    {
        $course_id = Request::get('course');

        if (! $course_id)
        {
            return null;
        }

        // Retrieve the users directly subscribed to the course
        $userConditions = array();
        $userConditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $userConditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER));

        $parameters = new DataClassDistinctParameters(
            new AndCondition($userConditions),
            new DataClassProperties(
                array(
                    new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID))));

        $user_ids = DataManager::distinct(
            \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation::class_name(),
            $parameters);

        // Retrieve the users subscribed through platform groups
        $groupConditions = array();
        $groupConditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $groupConditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP));

        $groups = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(
            Group::class_name(),
            new DataClassRetrievesParameters(
                new AndCondition($groupConditions),
                null,
                null,
                array(),
                new Joins(
                    new Join(
                        CourseEntityRelation::class_name(),
                        new EqualityCondition(
                            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID),
                            new PropertyConditionVariable(
                                CourseEntityRelation::class_name(),
                                CourseEntityRelation::PROPERTY_ENTITY_ID))))));

        $parameters = new DataClassDistinctParameters(
            new AndCondition($userConditions),
            new DataClassProperties(
                array(
                    new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID))));

        $groupIdentifiers = DataManager::distinct(
            \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation::class_name(),
            $parameters);

        $group_users = array();

        while ($group = $groups->next_result())
        {
            $group_user_ids = $this->getGroupSubscriptionService()->findUserIdsInGroupAndSubgroups($group);
            $group_users = array_merge($group_users, $group_user_ids);
        }

        $user_ids = array_unique(array_merge($user_ids, $group_users));

        if (count($user_ids) == 0)
        {
            return;
        }

        $search_query = Request::post('query');

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = Utilities::query_to_condition(
                $search_query,
                array(User::PROPERTY_USERNAME, User::PROPERTY_FIRSTNAME, User::PROPERTY_LASTNAME));
        }

        $conditions[] = new InCondition(User::PROPERTY_ID, $user_ids);

        $exclude = Request::post('exclude');

        if ($exclude)
        {
            if (! is_array($exclude))
            {
                $exclude = array($exclude);
            }

            $exclude_conditions = array();

            foreach ($exclude as $id)
            {
                $id = explode('_', $id);
                $exclude_condition = new NotCondition(
                    new EqualityCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                        new StaticConditionVariable($id[1])));

                $exclude_conditions[] = $exclude_condition;
            }

            if (count($exclude_conditions) > 0)
            {
                $conditions[] = new AndCondition($exclude_conditions);
            }
        }

        // Combine the conditions
        $count = count($conditions);
        if ($count > 1)
        {
            $condition = new AndCondition($conditions);
        }

        if ($count == 1)
        {
            $condition = $conditions[0];
        }

        return \Chamilo\Core\User\Storage\DataManager::retrieves(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            new DataClassRetrievesParameters(
                $condition,
                null,
                null,
                array(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))));
    }

    /**
     * @return GroupSubscriptionService
     */
    protected function getGroupSubscriptionService()
    {
        return $this->getService(GroupSubscriptionService::class);
    }
}