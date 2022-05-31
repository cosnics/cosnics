<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package group.lib
 */

/**
 *
 * @author Hans de Bisschop
 * @author Dieter De Neef
 * @author Sven Vanpoucke
 */
class Group extends NestedSet
{
    const PROPERTY_CODE = 'code';
    const PROPERTY_DATABASE_QUOTA = 'database_quota';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_DISK_QUOTA = 'disk_quota';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SORT = 'sort';

    /**
     * Array used to cache users in this group, depending on request (include subgroups, recursive subgroups)
     *
     * @var array
     */
    private $users;

    /**
     * Array used to cache user counts in this group, depending on request (include subgroups, recursive subgroups)
     *
     * @var array
     */
    private $user_count;

    /**
     * Array used to cache subgroups, depending on request (recursive or not)
     *
     * @var array
     */
    private $subgroups;

    private $subgroupIdentifiers;

    /**
     * Array used to cache subgroup counts, depending on request (recursive or not)
     *
     * @var array
     */
    private $subgroup_count;

    /**
     * Cache of the siblings of a group, depending on request (recursive or not)
     *
     * @var array
     */
    private $siblings;

    /**
     * @param bool $recursive
     *
     * @return mixed
     * @deprecated Use GroupService::countSubGroupsForGroup() now
     */
    public function count_subgroups($recursive = false)
    {
        if (!isset($this->subgroup_count[(int) $recursive]))
        {
            if ($recursive)
            {
                $this->subgroup_count[(int) $recursive] = ($this->get_right_value() - $this->get_left_value() - 1) / 2;
            }
            else
            {
                $children_condition = new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable($this->get_id())
                );
                $this->subgroup_count[(int) $recursive] = DataManager::count(
                    Group::class, new DataClassCountParameters($children_condition)
                );
            }
        }

        return $this->subgroup_count[(int) $recursive];
    }

    /**
     * @param bool $include_subgroups
     * @param bool $recursive_subgroups
     *
     * @return mixed
     * @deprecated Use GroupService::countUsersForGroup() now
     */
    public function count_users($include_subgroups = false, $recursive_subgroups = false)
    {
        if (!isset($this->user_count[(int) $include_subgroups][(int) $recursive_subgroups]))
        {
            if (!$include_subgroups)
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                    new StaticConditionVariable($this->get_id())
                );
                $parameters = new DataClassCountParameters(
                    $condition, null, new RetrieveProperties(
                        array(
                            new FunctionConditionVariable(
                                FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                                    GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID
                                )
                            )
                        )
                    )
                );
            }
            elseif ($include_subgroups && $recursive_subgroups)
            {
                $join = new Join(
                    Group::class, new EqualityCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_ID),
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID)
                    )
                );
                $joins = new Joins(array($join));

                $conditions = [];
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($this->get_left_value())
                );
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable($this->get_right_value())
                );
                $condition = new AndCondition($conditions);

                $parameters = new DataClassCountParameters(
                    $condition, $joins, new RetrieveProperties(
                        array(
                            new FunctionConditionVariable(
                                FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                                    GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID
                                )
                            )
                        )
                    )
                );
            }
            else
            {
                $join = new Join(
                    Group::class, new EqualityCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_ID),
                        new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID)
                    )
                );
                $joins = new Joins(array($join));

                $conditions = [];
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable($this->get_id())
                );
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_ID),
                    new StaticConditionVariable($this->get_id())
                );
                $condition = new OrCondition($conditions);

                $parameters = new DataClassCountParameters(
                    $condition, $joins, new RetrieveProperties(
                        array(
                            new FunctionConditionVariable(
                                FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                                    GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID
                                )
                            )
                        )
                    )
                );
            }

            $this->user_count[(int) $include_subgroups][(int) $recursive_subgroups] = DataManager::count(
                GroupRelUser::class, $parameters
            );
        }

        return $this->user_count[(int) $include_subgroups][(int) $recursive_subgroups];
    }

    /**
     * @param int $previous_id
     *
     * @return bool
     * @deprecated Use GroupService::createGroup() now
     */
    public function create($previous_id = 0, $reference_node = null): bool
    {
        $parent_id = $this->get_parent_id();

        if ($previous_id)
        {
            return parent::create(parent::AS_NEXT_SIBLING_OF, $previous_id);
        }
        else
        {
            return parent::create(parent::AS_LAST_CHILD_OF, $parent_id);
        }
    }

    /**
     * Instructs the DataManager to delete this group.
     *
     * @param $in_batch - delete groups in batch and fix nested values later
     *
     * @return boolean True if success, false otherwise.
     * @deprecated should use $this->delete() of self::deletes( $array ) instead
     */
    public function delete_group($in_batch = false)
    {
        return self::delete();
    }

    /**
     * Deletes all content related to a group: - Users that are part of a group - Meta-data describing the group -
     * Rights templates tied to a group In principle, these could be deleted implicitly when using foreign keys (the
     * group id) with cascading deletes.
     *
     * @deprecated Part of GroupService::deleteGroup() now
     */
    public function delete_related_content()
    {
        // First, truncate the group so that users are removed.
        return $this->truncate();
    }

    /**
     * @param bool $recursive
     *
     * @return mixed
     * @deprecated Use GroupService::findSubGroupIdentifiersForGroup() now
     */
    private function getSubgroupIdentifiers($recursive = false)
    {
        if (!isset($this->subgroupIdentifiers[(int) $recursive]))
        {
            if ($recursive)
            {
                $childrenCondition = [];
                $childrenCondition[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                    ComparisonCondition::GREATER_THAN, new StaticConditionVariable($this->get_left_value())
                );
                $childrenCondition[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                    ComparisonCondition::LESS_THAN, new StaticConditionVariable($this->get_right_value())
                );
                $childrenCondition = new AndCondition($childrenCondition);
            }
            else
            {
                $childrenCondition = new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable($this->get_id())
                );
            }

            $subgroupIdentifiers = DataManager::distinct(
                Group::class, new DataClassDistinctParameters(
                    $childrenCondition,
                    new RetrieveProperties(array(new PropertyConditionVariable(Group::class, Group::PROPERTY_ID)))
                )
            );

            $this->subgroupIdentifiers[(int) $recursive] = $subgroupIdentifiers;
        }

        return $this->subgroupIdentifiers[(int) $recursive];
    }

    /**
     * Get the group's children
     *
     * @deprecated Use GroupService::findSubGroupsForGroup() now
     */
    public function get_children($recursive = true)
    {
        return $this->get_subgroups($recursive);
    }

    public function get_code()
    {
        return $this->getDefaultProperty(self::PROPERTY_CODE);
    }

    /**
     * Returns the database quota for users in this group.
     *
     * @return Int the database quota
     */
    public function get_database_quota()
    {
        return $this->getDefaultProperty(self::PROPERTY_DATABASE_QUOTA);
    }

    /**
     * Get the default properties of all groups.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_NAME,
                self::PROPERTY_DESCRIPTION,
                self::PROPERTY_SORT,
                self::PROPERTY_CODE,
                self::PROPERTY_DISK_QUOTA,
                self::PROPERTY_DATABASE_QUOTA
            )
        );
    }

    /**
     * Returns the description of this group.
     *
     * @return String The description
     */
    public function get_description()
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the disk quota for users in this group.
     *
     * @return Int the disk quota
     */
    public function get_disk_quota()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISK_QUOTA);
    }

    /**
     * @param bool $include_self
     *
     * @return string
     * @deprecated Use GroupService::getFullyQualifiedNameForGroup() now
     */
    public function get_fully_qualified_name($include_self = true)
    {
        $parents = $this->get_parents($include_self);
        $names = [];

        foreach ($parents as $node)
        {
            $names[] = $node->get_name();
        }

        return implode(' <span class="text-primary">></span> ', array_reverse($names));
    }

    /**
     * Returns the name of this group.
     *
     * @return String The name
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     *
     * @deprecated use get_parent_id() instead.
     */
    public function get_parent()
    {
        return $this->get_parent_id();
    }

    /**
     * Get all of the group's parents
     *
     * @deprecated should use get_ancestors() instead
     */
    public function get_parents($include_self = true)
    {
        return $this->get_ancestors($include_self);
    }

    public function get_sort()
    {
        return $this->getDefaultProperty(self::PROPERTY_SORT);
    }

    /**
     * @param bool $recursive
     *
     * @return mixed
     * @deprecated Use GroupService::findSubGroupsForGroup() now
     */
    public function get_subgroups($recursive = false)
    {
        if (!isset($this->subgroups[(int) $recursive]))
        {
            if ($recursive)
            {
                $children_conditions = [];
                $children_conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_LEFT_VALUE),
                    ComparisonCondition::GREATER_THAN, new StaticConditionVariable($this->get_left_value())
                );
                $children_conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_RIGHT_VALUE),
                    ComparisonCondition::LESS_THAN, new StaticConditionVariable($this->get_right_value())
                );
                $children_condition = new AndCondition($children_conditions);
            }
            else
            {
                $children_condition = new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable($this->get_id())
                );
            }

            $groups = DataManager::retrieves(Group::class, $children_condition);

            $subgroups = [];

            foreach ($groups as $group)
            {
                $subgroups[$group->get_id()] = $group;
            }

            $this->subgroups[(int) $recursive] = $subgroups;
        }

        return $this->subgroups[(int) $recursive];
    }

    /**
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'group_group';
    }

    /**
     * @param bool $include_subgroups
     * @param bool $recursive_subgroups
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\InCondition
     * @deprecated Should not be used anymore
     */
    private function get_user_condition($include_subgroups = false, $recursive_subgroups = false)
    {
        if ($include_subgroups)
        {
            $groups = $this->getSubgroupIdentifiers($recursive_subgroups);

            if (!is_array($groups))
            {
                $groups = [];
            }
        }
        else
        {
            $groups = [];
        }

        $groups[] = $this->get_id();

        return new InCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID), $groups
        );
    }

    /**
     * @param bool $include_subgroups
     * @param bool $recursive_subgroups
     *
     * @return mixed
     * @deprecated Use GroupService::findUserIdentifiersForGroup() now
     */
    public function get_users($include_subgroups = false, $recursive_subgroups = false)
    {
        if (!isset($this->users[(int) $include_subgroups][(int) $recursive_subgroups]))
        {
            $condition = $this->get_user_condition($include_subgroups, $recursive_subgroups);
            $users = DataManager::distinct(
                GroupRelUser::class, new DataClassDistinctParameters(
                    $condition, new RetrieveProperties(
                        array(new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID))
                    )
                )
            );

            $this->users[(int) $include_subgroups][(int) $recursive_subgroups] = $users;
        }

        return $this->users[(int) $include_subgroups][(int) $recursive_subgroups];
    }

    /**
     *
     * @deprecated should use is_descendant_of
     */
    public function is_child_of($parent)
    {
        return $this->is_descendant_of($parent);
    }

    /**
     *
     * @deprecated Use Group::isAncestorOf()
     */
    public function is_parent_of($child)
    {
        return $this->is_ancestor_of($child);
    }

    /**
     * @param integer $new_parent_id
     * @param integer $new_previous_id
     *
     * @return boolean
     * @deprecated Use GroupService::moveGroup() now
     */
    public function move($new_parent_id = 0, $new_previous_id = null, $condition = null)
    {
        if ($new_previous_id != 0)
        {
            return parent::move(self::AS_NEXT_SIBLING_OF, $new_previous_id);
        }
        else
        {
            return parent::move(self::AS_LAST_CHILD_OF, $new_parent_id);
        }
    }

    public function set_code($code)
    {
        $this->setDefaultProperty(self::PROPERTY_CODE, $code);
    }

    public function set_database_quota($database_quota)
    {
        $this->setDefaultProperty(self::PROPERTY_DATABASE_QUOTA, $database_quota);
    }

    public function set_description($description)
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    public function set_disk_quota($disk_quota)
    {
        $this->setDefaultProperty(self::PROPERTY_DISK_QUOTA, $disk_quota);
    }

    /**
     * Sets the name of this group.
     *
     * @param String $name the name.
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     *
     * @deprecated use set_parent_id() || move instead.
     */
    public function set_parent($parent)
    {
        $this->set_parent_id($parent);
    }

    public function set_sort($sort)
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);
    }

    /**
     * Unsubscribes all users from this group
     *
     * @deprecated Part of GroupService::deleteGroup() now
     */
    public function truncate()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($this->get_id())
        );

        return DataManager::deletes(GroupRelUser::class, $condition);
    }
}
