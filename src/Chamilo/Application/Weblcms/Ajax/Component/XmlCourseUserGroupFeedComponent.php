<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class XmlCourseUserGroupFeedComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{

    public function run()
    {
        $course = Request :: get('course');
        $show_groups = Request :: get('show_groups');

        if ($course)
        {
            $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_by_id(
                Course :: class_name(),
                $course);

            $query = Request :: get('query');
            $exclude = Request :: get('exclude');

            $user_conditions = array();
            $group_conditions = array();

            if ($query)
            {
                $q = '*' . $query . '*';

                $user_conditions[] = Utilities :: query_to_condition(
                    $query,
                    array(User :: PROPERTY_USERNAME, User :: PROPERTY_FIRSTNAME, User :: PROPERTY_LASTNAME));

                $group_conditions[] = new PatternMatchCondition(CourseGroup :: PROPERTY_NAME, $q);
            }

            if ($exclude)
            {
                if (! is_array($exclude))
                {
                    $exclude = array($exclude);
                }

                $exclude_conditions = array();
                $exclude_conditions['user'] = array();
                $exclude_conditions['group'] = array();

                foreach ($exclude as $id)
                {
                    $id = explode('_', $id);

                    if ($id[0] == 'user')
                    {
                        $condition = new NotCondition(
                            new EqualityCondition(
                                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                                new StaticConditionVariable($id[1])));
                    }
                    elseif ($id[0] == 'group')
                    {
                        $condition = new NotCondition(
                            new EqualityCondition(
                                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_ID),
                                new StaticConditionVariable($id[1])));
                    }

                    $exclude_conditions[$id[0]][] = $condition;
                }

                if (count($exclude_conditions['user']) > 0)
                {
                    $user_conditions[] = new AndCondition($exclude_conditions['user']);
                }

                if (count($exclude_conditions['group']) > 0)
                {
                    $group_conditions[] = new AndCondition($exclude_conditions['group']);
                }
            }

            // if ($group_conditions)
            if (count($group_conditions) > 0)
            {
                $group_condition = new AndCondition($group_conditions);
            }
            else
            {
                // $group_condition = null;
                $group_condition = new EqualityCondition(
                    new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_COURSE_CODE),
                    new StaticConditionVariable($course->get_id()));
            }

            $relation_condition = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseUserRelation :: class_name(),
                    CourseUserRelation :: PROPERTY_COURSE_ID),
                new StaticConditionVariable($course->get_id()));

            $course_user_relation_result_set = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
                CourseUserRelation :: class_name(),
                $relation_condition);

            $user_ids = array();
            while ($course_user = $course_user_relation_result_set->next_result())
            {
                $user_ids[] = $course_user->get_user_id();
            }

            // Add users from subscribed platform groups to user ids array
            $group_relations = $course->get_subscribed_groups();

            if (count($group_relations) > 0)
            {
                $group_users = array();

                foreach ($group_relations as $group_relation)
                {
                    $group = $group_relation->get_group();
                    $group_user_ids = $group->get_users(true, true);

                    $group_users = array_merge($group_users, $group_user_ids);
                }

                $user_ids = array_merge($user_ids, $group_users);
            }

            // if ($user_conditions)
            if (count($user_conditions) > 0)
            {
                $user_conditions[] = new InCondition(User :: PROPERTY_ID, $user_ids);
                $user_condition = new AndCondition($user_conditions);
            }
            else
            {
                if (count($user_ids) > 0)
                {
                    $user_condition = new InCondition(User :: PROPERTY_ID, $user_ids);
                }
                else
                {
                    $user_condition = null;
                }
            }

            // Order the users alphabetically
            $format = PlatformSetting :: get_instance()->get_value('fullname_format', User :: CONTEXT);
            $order = array(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME),
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME));
            if ($format == User :: NAME_FORMAT_LAST)
            {
                $order = array_reverse($order);
            }

            $user_result_set = \Chamilo\Core\User\Storage\DataManager :: retrieves(
                User :: class_name(),
                new DataClassRetrievesParameters($user_condition, null, null, $order));

            $users = array();
            while ($user = $user_result_set->next_result())
            {
                $users[] = $user;
            }

            if ($show_groups)
            {
                $groups = array();

                $group_result_set = DataManager :: retrieves(
                    CourseGroup :: class_name(),
                    new DataClassRetrievesParameters(
                        $group_condition,
                        null,
                        null,
                        array(new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_NAME))));

                while ($group = $group_result_set->next_result())
                {

                    $group_parent_id = $group->get_parent_id();

                    if (! is_array($groups[$group_parent_id]))
                    {
                        $groups[$group_parent_id] = array();
                    }

                    if (! isset($groups[$group_parent_id][$group->get_id()]))
                    {
                        $groups[$group_parent_id][$group->get_id()] = $group;
                    }

                    if ($group_parent_id != 0)
                    {
                        $tree_parents = $group->get_parents(false);

                        foreach ($tree_parents as $tree_parent)
                        {
                            $tree_parent_parent_id = $tree_parent->get_parent_id();

                            if (! is_array($groups[$tree_parent_parent_id]))
                            {
                                $groups[$tree_parent_parent_id] = array();
                            }

                            if (! isset($groups[$tree_parent_parent_id][$tree_parent->get_id()]))
                            {
                                $groups[$tree_parent_parent_id][$tree_parent->get_id()] = $tree_parent;
                            }
                        }
                    }
                    else
                    {
                        $top_group_parent_id = $group->get_id();
                    }
                }

                $groups_tree = $this->get_group_tree($top_group_parent_id, $groups);
            }
            else
            {
                $groups_tree = array();
            }
        }
        else
        {
            $users = array();
            $groups_tree = array();
        }

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>', "\n", '<tree>', "\n";

        $this->dump_tree($users, $groups_tree);

        echo '</tree>';
    }

    function dump_tree($users, $groups_tree)
    {
        global $group_users, $show_groups;

        if ($this->contains_results($users) || $this->contains_results($groups_tree))
        {
            if ($this->contains_results($users))
            {
                echo '<node id="user" classes="category unlinked" title="';
                echo Translation :: get('Users', null, 'user') . '">', "\n";
                foreach ($users as $user)
                {
                    if (in_array($user->get_id(), $group_users))
                    {
                        $class = 'type type_user_platform';
                    }
                    else
                    {
                        $class = 'type type_user';
                    }

                    echo '<leaf id="user_' . $user->get_id() . '" classes="' . $class . '" title="';
                    echo htmlspecialchars($user->get_fullname()) . '" description="';
                    echo htmlspecialchars($user->get_username()) . '"/>' . "\n";
                }
                echo '</node>', "\n";
            }

            if ($show_groups)
            {
                $this->dump_platform_groups_tree();

                if ($this->contains_results($groups_tree))
                {
                    global $course;

                    echo '<node id="group" classes="category unlinked" title="' . $course->get_title() . '">', "\n";

                    $this->dump_groups_tree($groups_tree);
                    echo '</node>', "\n";
                }
            }
        }
    }

    function dump_platform_groups_tree()
    {
        global $course;
        $group_relations = $course->get_subscribed_groups();

        if (count($group_relations) > 0)
        {
            echo '<node id="platform" classes="category unlinked" title="';
            echo Translation :: get('LinkedPlatformGroups') . '">', "\n";

            foreach ($group_relations as $group_relation)
            {
                $group = $group_relation->get_group();
                $this->dump_platform_group($group);
            }

            echo '</node>', "\n";
        }
    }

    function dump_platform_group($group)
    {
        $children = $group->get_children(false);

        if ($children && count($children) > 0)
        {
            echo '<node id="platform_' . $group->get_id() . '" classes="type type_group" title="';
            echo htmlspecialchars($group->get_name()) . '" description="';
            echo htmlspecialchars($group->get_name()) . '">' . "\n";

            foreach ($children as $child)
            {
                $this->dump_platform_group($child);
            }

            echo '</node>';
        }
        else
        {
            echo '<leaf id="platform_' . $group->get_id() . '" classes="type type_group" title="';
            echo htmlspecialchars($group->get_name()) . '" description="';
            echo htmlspecialchars($group->get_name()) . '"/>' . "\n";
        }
    }

    function dump_groups_tree($groups)
    {
        foreach ($groups as $group)
        {
            if ($this->contains_results($group['children']))
            {
                echo '<node id="group_' . $group['group']->get_id() . '" classes="type type_group" title="';
                echo htmlspecialchars($group['group']->get_name()) . '" description="';
                echo htmlspecialchars($group['group']->get_name()) . '">', "\n";
                $this->dump_groups_tree($group['children']);
                echo '</node>', "\n";
            }
            else
            {
                echo '<leaf id="group_' . $group['group']->get_id() . '" classes="' . 'type type_group' . '" title="';
                echo htmlspecialchars($group['group']->get_name()) . '" description="';
                echo htmlspecialchars($group['group']->get_name()) . '"/>' . "\n";
            }
        }
    }

    function get_group_tree($index, $groups)
    {
        $tree = array();
        foreach ($groups[$index] as $child)
        {
            $tree[] = array('group' => $child, 'children' => $this->get_group_tree($child->get_id(), $groups));
        }
        return $tree;
    }

    function contains_results($objects)
    {
        if (count($objects))
        {
            return true;
        }
        return false;
    }
}
