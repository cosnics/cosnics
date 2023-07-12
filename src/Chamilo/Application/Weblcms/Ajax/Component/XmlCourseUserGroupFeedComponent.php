<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

class XmlCourseUserGroupFeedComponent extends Manager
{

    private $course;

    private $courseIdentifier;

    private $group_users;

    private $show_groups;

    public function run()
    {
        $this->courseIdentifier = Request::get('course');
        $this->show_groups = Request::get('show_groups');

        if ($this->courseIdentifier)
        {
            $this->course = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_by_id(
                Course::class, $this->courseIdentifier
            );

            $query = Request::get('query');
            $exclude = Request::get('exclude');

            $user_conditions = [];
            $group_conditions = [];

            if ($query)
            {
                $userCondition = [];
                $userCondition[] = new ContainsCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), $query
                );
                $userCondition[] = new ContainsCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), $query
                );
                $userCondition[] = new ContainsCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), $query
                );
                $user_conditions[] = new OrCondition($userCondition);

                $group_conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_NAME), $query
                );
            }

            if ($exclude)
            {
                if (!is_array($exclude))
                {
                    $exclude = [$exclude];
                }

                $exclude_conditions = [];
                $exclude_conditions['user'] = [];
                $exclude_conditions['group'] = [];

                foreach ($exclude as $id)
                {
                    $id = explode('_', $id);

                    if ($id[0] == 'user')
                    {
                        $condition = new NotCondition(
                            new EqualityCondition(
                                new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                                new StaticConditionVariable($id[1])
                            )
                        );
                    }
                    elseif ($id[0] == 'group')
                    {
                        $condition = new NotCondition(
                            new EqualityCondition(
                                new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_ID),
                                new StaticConditionVariable($id[1])
                            )
                        );
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
                    new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_COURSE_CODE),
                    new StaticConditionVariable($this->course->get_id())
                );
            }

            $userConditions = [];
            $userConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                ), new StaticConditionVariable($this->course->getId())
            );
            $userConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER)
            );

            $parameters = new DataClassDistinctParameters(
                new AndCondition($userConditions), new RetrieveProperties(
                    [
                        new PropertyConditionVariable(
                            CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                        )
                    ]
                )
            );

            $user_ids = DataManager::distinct(CourseEntityRelation::class, $parameters);

            // Add users from subscribed platform groups to user ids array
            $group_relations = $this->course->get_subscribed_groups();

            if (count($group_relations) > 0)
            {
                $this->group_users = [];

                foreach ($group_relations as $group_relation)
                {
                    // var_dump($group_relation);
                    $group = DataManager::retrieve_by_id(Group::class, $group_relation->getEntityId());
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
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID), $user_ids
                );
                $user_condition = new AndCondition($user_conditions);
            }
            else
            {
                if (count($user_ids) > 0)
                {
                    $user_condition = new InCondition(
                        new PropertyConditionVariable(User::class, User::PROPERTY_ID), $user_ids
                    );
                }
                else
                {
                    $user_condition = null;
                }
            }

            // Order the users alphabetically
            $order = [
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), SORT_ASC),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), SORT_ASC)
            ];

            $user_result_set = \Chamilo\Core\User\Storage\DataManager::retrieves(
                User::class, new DataClassRetrievesParameters($user_condition, null, null, new OrderBy($order))
            );

            $users = [];
            foreach ($user_result_set as $user)
            {
                $users[] = $user;
            }

            if ($this->show_groups)
            {
                $groups = [];

                $group_result_set = DataManager::retrieves(
                    CourseGroup::class, new DataClassRetrievesParameters(
                        $group_condition, null, null, new OrderBy([
                            new OrderProperty(
                                new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_NAME)
                            )
                        ])
                    )
                );

                foreach ($group_result_set as $group)
                {

                    $group_parent_id = $group->get_parent_id();

                    if (!is_array($groups[$group_parent_id]))
                    {
                        $groups[$group_parent_id] = [];
                    }

                    if (!isset($groups[$group_parent_id][$group->get_id()]))
                    {
                        $groups[$group_parent_id][$group->get_id()] = $group;
                    }

                    if ($group_parent_id != 0)
                    {
                        $tree_parents = $group->get_ancestors(false);

                        foreach ($tree_parents as $tree_parent)
                        {
                            $tree_parent_parent_id = $tree_parent->get_parent_id();

                            if (!is_array($groups[$tree_parent_parent_id]))
                            {
                                $groups[$tree_parent_parent_id] = [];
                            }

                            if (!isset($groups[$tree_parent_parent_id][$tree_parent->get_id()]))
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
                $groups_tree = [];
            }
        }
        else
        {
            $users = [];
            $groups_tree = [];
        }

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>', PHP_EOL, '<tree>', PHP_EOL;

        $this->dump_tree($users, $groups_tree);

        echo '</tree>';
    }

    public function contains_results($objects)
    {
        if (count($objects))
        {
            return true;
        }

        return false;
    }

    public function dump_groups_tree($groups)
    {
        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');

        foreach ($groups as $group)
        {
            if ($this->contains_results($group['children']))
            {
                echo '<node id="group_' . $group['group']->get_id() . '" classes="' . $glyph->getClassNamesString() .
                    '" title="';
                echo htmlspecialchars($group['group']->get_name()) . '" description="';
                echo htmlspecialchars($group['group']->get_name()) . '">', PHP_EOL;
                $this->dump_groups_tree($group['children']);
                echo '</node>', PHP_EOL;
            }
            else
            {
                echo '<leaf id="group_' . $group['group']->get_id() . '" classes="' . $glyph->getClassNamesString() .
                    '" title="';
                echo htmlspecialchars($group['group']->get_name()) . '" description="';
                echo htmlspecialchars($group['group']->get_name()) . '"/>' . PHP_EOL;
            }
        }
    }

    public function dump_platform_group($group)
    {
        $children = $group->get_subgroups();
        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');

        if ($children && count($children) > 0)
        {
            echo '<node id="platform_' . $group->get_id() . '" classes="' . $glyph->getClassNamesString() . '" title="';
            echo htmlspecialchars($group->get_name()) . '" description="';
            echo htmlspecialchars($group->get_name()) . '">' . PHP_EOL;

            foreach ($children as $child)
            {
                $this->dump_platform_group($child);
            }

            echo '</node>';
        }
        else
        {
            echo '<leaf id="platform_' . $group->get_id() . '" classes="' . $glyph->getClassNamesString() . '" title="';
            echo htmlspecialchars($group->get_name()) . '" description="';
            echo htmlspecialchars($group->get_name()) . '"/>' . PHP_EOL;
        }
    }

    public function dump_platform_groups_tree()
    {
        $group_relations = $this->course->get_subscribed_groups();

        if (count($group_relations) > 0)
        {
            $glyph = new FontAwesomeGlyph('folder', ['unlinked'], null, 'fas');
            echo '<node id="platform" classes="' . $glyph->getClassNamesString() . '" title="';
            echo Translation::get('LinkedPlatformGroups') . '">', PHP_EOL;

            foreach ($group_relations as $group_relation)
            {
                $group = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
                    Group::class, $group_relation->getEntityId()
                );

                if ($group instanceof Group)
                {
                    $this->dump_platform_group($group);
                }
            }

            echo '</node>', PHP_EOL;
        }
    }

    public function dump_tree($users, $groups_tree)
    {
        if ($this->contains_results($users) || $this->contains_results($groups_tree))
        {
            if ($this->contains_results($users))
            {
                $glyph = new FontAwesomeGlyph('folder', ['unlinked'], null, 'fas');
                echo '<node id="user" classes="' . $glyph->getClassNamesString() . '" title="';
                echo Translation::get('Users', null, 'user') . '">', PHP_EOL;

                $userGlyph = new FontAwesomeGlyph('user', [], null, 'fas');
                $platformUserGlyph = new FontAwesomeGlyph('user-tie', [], null, 'fas');

                foreach ($users as $user)
                {
                    $isPlatformUser = in_array($user->get_id(), $this->group_users);

                    echo '<leaf id="user_' . $user->get_id() . '" classes="' .
                        ($isPlatformUser ? $platformUserGlyph->getClassNamesString() :
                            $userGlyph->getClassNamesString()) . '" title="';
                    echo htmlspecialchars($user->get_fullname()) . '" description="';
                    echo htmlspecialchars($user->get_username()) . '"/>' . PHP_EOL;
                }
                echo '</node>', PHP_EOL;
            }

            if ($this->show_groups)
            {
                $this->dump_platform_groups_tree();

                if ($this->contains_results($groups_tree))
                {
                    $glyph = new FontAwesomeGlyph('folder', ['unlinked'], null, 'fas');
                    echo '<node id="group" classes="' . $glyph->getClassNamesString() . '" title="' .
                        htmlspecialchars($this->course->get_title()) . '">', PHP_EOL;

                    $this->dump_groups_tree($groups_tree);
                    echo '</node>', PHP_EOL;
                }
            }
        }
    }

    public function get_group_tree($index, $groups)
    {
        $tree = [];
        foreach ($groups[$index] as $child)
        {
            $tree[] = ['group' => $child, 'children' => $this->get_group_tree($child->get_id(), $groups)];
        }

        return $tree;
    }
}
