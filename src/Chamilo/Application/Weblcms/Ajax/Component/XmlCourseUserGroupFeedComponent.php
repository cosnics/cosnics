<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class XmlCourseUserGroupFeedComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{

    private $courseIdentifier;

    private $course;

    private $show_groups;

    private $group_users;

    public function run()
    {
        $this->courseIdentifier = Request::get('course');
        $this->show_groups = Request::get('show_groups');

        if ($this->courseIdentifier)
        {
            $this->course = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_by_id(
                Course::class_name(),
                $this->courseIdentifier);

            $query = Request::get('query');
            $exclude = Request::get('exclude');

            $user_conditions = array();
            $group_conditions = array();

            if ($query)
            {
                $q = '*' . $query . '*';

                $userCondition = array();
                $userCondition[] = new PatternMatchCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME),
                    $q);
                $userCondition[] = new PatternMatchCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                    $q);
                $userCondition[] = new PatternMatchCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                    $q);
                $user_conditions[] = new OrCondition($userCondition);

                $group_conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME),
                    $q);
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
                                new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                                new StaticConditionVariable($id[1])));
                    }
                    elseif ($id[0] == 'group')
                    {
                        $condition = new NotCondition(
                            new EqualityCondition(
                                new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_ID),
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
                    new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_COURSE_CODE),
                    new StaticConditionVariable($this->course->get_id()));
            }

            $userConditions = array();
            $userConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(),
                    CourseEntityRelation::PROPERTY_COURSE_ID),
                new StaticConditionVariable($this->course->getId()));
            $userConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class_name(),
                    CourseEntityRelation::PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER));

            $parameters = new DataClassDistinctParameters(
                new AndCondition($userConditions),
                CourseEntityRelation::PROPERTY_ENTITY_ID);

            $user_ids = DataManager::distinct(CourseEntityRelation::class_name(), $parameters);

            // Add users from subscribed platform groups to user ids array
            $group_relations = $this->course->get_subscribed_groups();

            if (count($group_relations) > 0)
            {
                $this->group_users = array();

                foreach ($group_relations as $group_relation)
                {
                    // var_dump($group_relation);
                    $group = DataManager::retrieve_by_id(Group::class_name(), $group_relation->getEntityId());
                    // var_dump($group);

                    if ($group instanceof Group)
                    {
                        $group_user_ids = $group->get_users(true, true);

                        $this->group_users = array_merge($this->group_users, $group_user_ids);
                    }
                }

                $user_ids = array_merge($user_ids, $this->group_users);
            }

            // if ($user_conditions)
            if (count($user_conditions) > 0)
            {
                $user_conditions[] = new InCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                    $user_ids);
                $user_condition = new AndCondition($user_conditions);
            }
            else
            {
                if (count($user_ids) > 0)
                {
                    $user_condition = new InCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                        $user_ids);
                }
                else
                {
                    $user_condition = null;
                }
            }

            // Order the users alphabetically
            $format = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'fullname_format'));
            $order = array(
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME), SORT_ASC),
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME), SORT_ASC));

            if ($format == User::NAME_FORMAT_LAST)
            {
                $order = array_reverse($order);
            }

            $user_result_set = \Chamilo\Core\User\Storage\DataManager::retrieves(
                User::class_name(),
                new DataClassRetrievesParameters($user_condition, null, null, $order));

            $users = array();
            while ($user = $user_result_set->next_result())
            {
                $users[] = $user;
            }

            if ($this->show_groups)
            {
                $groups = array();

                $group_result_set = DataManager::retrieves(
                    CourseGroup::class_name(),
                    new DataClassRetrievesParameters(
                        $group_condition,
                        null,
                        null,
                        array(
                            new OrderBy(
                                new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME)))));

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
        if ($this->contains_results($users) || $this->contains_results($groups_tree))
        {
            if ($this->contains_results($users))
            {
                echo '<node id="user" classes="category unlinked" title="';
                echo Translation::get('Users', null, 'user') . '">', "\n";
                foreach ($users as $user)
                {
                    if (in_array($user->get_id(), $this->group_users))
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

            if ($this->show_groups)
            {
                $this->dump_platform_groups_tree();

                if ($this->contains_results($groups_tree))
                {
                    echo '<node id="group" classes="category unlinked" title="' .
                         htmlspecialchars($this->course->get_title()) . '">', "\n";

                    $this->dump_groups_tree($groups_tree);
                    echo '</node>', "\n";
                }
            }
        }
    }

    function dump_platform_groups_tree()
    {
        $group_relations = $this->course->get_subscribed_groups();

        if (count($group_relations) > 0)
        {
            echo '<node id="platform" classes="category unlinked" title="';
            echo Translation::get('LinkedPlatformGroups') . '">', "\n";

            foreach ($group_relations as $group_relation)
            {
                $group = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
                    Group::class_name(),
                    $group_relation->getEntityId());

                if ($group instanceof Group)
                {
                    $this->dump_platform_group($group);
                }
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
