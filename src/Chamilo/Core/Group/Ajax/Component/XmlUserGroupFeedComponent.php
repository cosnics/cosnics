<?php
namespace Chamilo\Core\Group\Ajax\Component;

use Chamilo\Core\Group\Ajax\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Group\XmlFeeds
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class XmlUserGroupFeedComponent extends Manager
{

    public function run()
    {
        $query = Request::get('query');
        $exclude = Request::get('exclude');

        $user_conditions = array();
        $group_conditions = array();

        if ($query)
        {
            $q = '*' . $query . '*';

            $user_conditions[] = new OrCondition(
                array(
                    new PatternMatchCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME), $q
                    ),
                    new PatternMatchCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME), $q
                    ),
                    new PatternMatchCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME), $q
                    )
                )
            );
            $group_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME), $q
            );
        }

        if ($exclude)
        {
            if (!is_array($exclude))
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
                            new StaticConditionVariable($id[1])
                        )
                    );
                }
                elseif ($id[0] == 'group')
                {
                    $condition = new NotCondition(
                        new EqualityCondition(
                            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID),
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

        if (count($group_conditions) > 0)
        {
            $group_condition = new AndCondition($group_conditions);
        }
        else
        {
            $group_condition = null;
        }

        $groups = array();
        $allowed_users = array();

        $group_result_set = \Chamilo\Core\Group\Storage\DataManager::retrieves(
            Group::class_name(), new DataClassRetrievesParameters(
                $group_condition, null, null,
                array(new OrderBy(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME)))
            )
        );

        while ($group = $group_result_set->next_result())
        {
            $group_parent_id = $group->get_parent();

            if (!is_array($groups[$group_parent_id]))
            {
                $groups[$group_parent_id] = array();
            }

            if (!isset($groups[$group_parent_id][$group->get_id()]))
            {
                $groups[$group_parent_id][$group->get_id()] = $group;
            }

            if ($group_parent_id != 0)
            {
                $tree_parents = $group->get_parents(false);

                while ($tree_parent = $tree_parents->next_result())
                {
                    $tree_parent_parent_id = $tree_parent->get_parent();

                    if (!is_array($groups[$tree_parent_parent_id]))
                    {
                        $groups[$tree_parent_parent_id] = array();
                    }

                    if (!isset($groups[$tree_parent_parent_id][$tree_parent->get_id()]))
                    {
                        $groups[$tree_parent_parent_id][$tree_parent->get_id()] = $tree_parent;
                    }
                }
            }
        }

        $groups_tree = $this->get_group_tree(0, $groups);

        if (count($user_conditions) > 0)
        {
            $user_condition = new AndCondition($user_conditions);
        }
        else
        {
            $user_condition = null;
        }

        $user_result_set = DataManager::retrieves(
            User::class_name(), new DataClassRetrievesParameters(
                $user_condition, null, null, array(
                    new OrderBy(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME)
                    )
                )
            )
        );

        $users = array();
        while ($user = $user_result_set->next_result())
        {
            $users[] = $user;
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
        $glyph = new FontAwesomeGlyph('users', array(), null, 'fas');

        foreach ($groups as $group)
        {
            $description =
                strip_tags($group['group']->get_fully_qualified_name() . ' [' . $group['group']->get_code() . ']');

            if ($this->contains_results($group['children']))
            {
                echo '<node id="group_' . $group['group']->get_id() . '" classes="' . $glyph->getClassNamesString() .
                    '" title="' . htmlspecialchars($group['group']->get_name()) . '" description="' .
                    htmlspecialchars($description) . '">', PHP_EOL;
                $this->dump_groups_tree($group['children']);
                echo '</node>', PHP_EOL;
            }
            else
            {
                echo '<leaf id="group_' . $group['group']->get_id() . '" classes="' . $glyph->getClassNamesString() .
                    '" title="' . htmlspecialchars($group['group']->get_name()) . '" description="' .
                    htmlspecialchars($description) . '"/>' . PHP_EOL;
            }
        }
    }

    public function dump_tree($users, $groups)
    {
        if ($this->contains_results($users) || $this->contains_results($groups))
        {
            if ($this->contains_results($users))
            {
                $glyph = new FontAwesomeGlyph('folder', array('unlinked'), null, 'fas');
                echo '<node id="user" classes="' . $glyph->getClassNamesString() . '" title="Users">', PHP_EOL;
                $glyph = new FontAwesomeGlyph('user', array(), null, 'fas');

                foreach ($users as $user)
                {
                    echo '<leaf id="user_' . $user->get_id() . '" classes="' . $glyph->getClassNamesString() .
                        '" title="' . htmlspecialchars($user->get_fullname()) . '" description="' .
                        htmlentities($user->get_username()) . '"/>' . PHP_EOL;
                }
                echo '</node>', PHP_EOL;
            }

            if ($this->contains_results($groups))
            {
                $this->dump_groups_tree($groups);
            }
        }
    }

    public function get_group_tree($index, $groups)
    {
        $tree = array();
        foreach ($groups[$index] as $child)
        {
            $tree[] = array('group' => $child, 'children' => $this->get_group_tree($child->get_id(), $groups));
        }

        return $tree;
    }
}