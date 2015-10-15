<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseGroupRelation;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

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
        $course_id = Request :: get('course');

        if (! $course_id)
        {
            return null;
        }

        // Retrieve the users directly subscribed to the course
        $relation_condition = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));

        $course_user_relation_result_set = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            CourseUserRelation :: class_name(),
            new DataClassRetrievesParameters($relation_condition));

        $user_ids = array();
        while ($course_user = $course_user_relation_result_set->next_result())
        {
            $user_ids[] = $course_user->get_user();
        }

        // Retrieve the users subscribed through platform groups
        $relation_condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroupRelation :: class_name(), CourseGroupRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));

        $course_group_relations = $course_group_relations = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            CourseGroupRelation :: class_name(),
            $relation_condition);

        $group_users = array();

        while ($group_relation = $course_group_relations->next_result())
        {
            $group = $group_relation->get_group_object();
            $group_user_ids = $group->get_users(true, true);

            $group_users = array_merge($group_users, $group_user_ids);
        }

        $user_ids = array_merge($user_ids, $group_users);

        if (count($user_ids) == 0)
        {
            return;
        }

        $search_query = Request :: post('query');

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = Utilities :: query_to_condition(
                $search_query,
                array(User :: PROPERTY_USERNAME, User :: PROPERTY_FIRSTNAME, User :: PROPERTY_LASTNAME));
        }

        $conditions[] = new InCondition(User :: PROPERTY_ID, $user_ids);

        $exclude = Request :: post('exclude');

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
                        new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
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

        return \Chamilo\Core\User\Storage\DataManager :: retrieves(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            new DataClassRetrievesParameters(
                $condition,
                null,
                null,
                array(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME),
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME))));
    }
}