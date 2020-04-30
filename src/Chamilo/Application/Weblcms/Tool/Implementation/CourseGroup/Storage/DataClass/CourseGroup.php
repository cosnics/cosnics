<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\NestedTreeNode;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.course_group
 */

/**
 * This class represents a course_group of users in a course in the weblcms.
 * To access the values of the properties,
 * this class and its subclasses should provide accessor methods. The names of the properties should be defined as class
 * constants, for standardization purposes. It is recommended that the names of these constants start with the string
 * "PROPERTY_".
 */
class CourseGroup extends NestedTreeNode
{
    const PROPERTY_COURSE_CODE = 'course_id';

    const PROPERTY_DESCRIPTION = 'description';

    const PROPERTY_GROUP_ID = "group_id";

    /**
     * If this course group has child course groups, this setting determines to how many of its direct children members
     * can be subscribed to at any one time.
     */
    const PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER = 'max_number_of_course_group_per_member';

    const PROPERTY_MAX_NUMBER_OF_MEMBERS = 'max_number_of_members';

    const PROPERTY_NAME = 'name';

    const PROPERTY_RANDOM_REG = 'random_registration_allowed';
    // random registration = randomly selects from the subscribed users of the
    // course and registers for the course_group

    const PROPERTY_SELF_REG = 'self_reg_allowed';

    const PROPERTY_SELF_UNREG = 'self_unreg_allowed';

    /**
     * Stores the members of this course group in a cache variable for multiple use
     *
     * @var <type>
     */
    private $members_cache;

    public function check_before_save()
    {
        $children = DataManager::count_course_group_users($this->get_id());

        if ($this->get_max_number_of_members() > 0 && $children > $this->get_max_number_of_members())
        {
            $this->add_error(Translation::get('MaximumMembersToSmall'));

            return false;
        }

        return true;
    }

    public function count_members()
    {
        $members = $this->get_members();

        return count($members);
    }

    public function create()
    {
        if (!$this->get_parent_id())
        {
            $root_group = DataManager::retrieve_course_group_root($this->get_course_code());
            if ($root_group)
            {
                $this->set_parent_id($root_group->get_id());
            }
        }

        return parent::create();
    }

    /**
     * @return string[]
     */
    public function getSubTreePropertyNames()
    {
        return array(CourseGroup::PROPERTY_COURSE_CODE);
    }

    /**
     * Gets the course code of the course in which this course_group was created
     *
     * @return string
     */
    public function get_course_code()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_CODE);
    }

    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Get the default properties of all course_groups.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_ID, self::PROPERTY_COURSE_CODE, self::PROPERTY_NAME, self::PROPERTY_DESCRIPTION,
                self::PROPERTY_MAX_NUMBER_OF_MEMBERS, self::PROPERTY_SELF_REG, self::PROPERTY_SELF_UNREG,
                self::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER, self::PROPERTY_RANDOM_REG
            )
        );
    }

    /**
     * Get the dependencies for this object
     *
     * @param array $dependencies
     *
     * @return bool
     */
    protected function get_dependencies($dependencies = array())
    {
        return array(
            CourseGroupUserRelation::class => new EqualityCondition(
                new PropertyConditionVariable(
                    CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_COURSE_GROUP
                ), new StaticConditionVariable($this->get_id())
            )
        );
    }

    /**
     * Gets the description of this course_group
     *
     * @return string
     */
    public function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Gets the group_id of this course_group
     *
     * @return int
     */
    public function get_group_id()
    {
        // return $this->get_default_property(self :: PROPERTY_GROUP_ID);
        return $this->get_optional_property(self::PROPERTY_GROUP_ID);
    }

    /**
     * Gets the id of this course_group
     *
     * @return int
     */
    public function get_id()
    {
        return $this->get_default_property(self::PROPERTY_ID);
    }

    /**
     * Get the maximum amount of child course groups that users can be subscribed to.
     *
     * @return int null null, no limit is set to the number of child course groups
     */
    public function get_max_number_of_course_group_per_member()
    {
        return $this->get_default_property(self::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER);
    }

    /**
     * Gets the maximum number of members than can be subscribed to this course_group
     *
     * @return int null null, no limit is set to the number of members
     */
    public function get_max_number_of_members()
    {
        return $this->get_default_property(self::PROPERTY_MAX_NUMBER_OF_MEMBERS);
    }

    /**
     * Retrieves the users subscribed to this course_group and/or it's children
     *
     * @param $include_subgroups boolean - Include the children of the subgroups
     * @param $recursive_subgroups boolean - Include the direct subgroups or include all the subgroups
     * @param $include_users boolean - Includes the users as real user objects
     *
     * @return User[] | int[]
     */
    public function get_members($include_subgroups = false, $recursive_subgroups = false, $include_users = false)
    {
        if (!isset(
            $this->members_cache[(int) $this->get_id(
            )][(int) $include_subgroups][(int) $recursive_subgroups][(int) $include_users]
        ))
        {
            $condition = $this->get_members_condition($include_subgroups, $recursive_subgroups);

            $course_group_user_relations = DataManager::retrieves(
                CourseGroupUserRelation::class, new DataClassRetrievesParameters($condition)
            );

            $users = array();

            while ($relation = $course_group_user_relations->next_result())
            {
                if ($include_users)
                {
                    $users[$relation->get_user()] = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                        User::class_name(), $relation->get_user()
                    );
                }
                else
                {
                    $users[$relation->get_user()] = $relation->get_user();
                }
            }
            if (!empty($users))
            {
                $this->members_cache[(int) $this->get_id(
                )][(int) $include_subgroups][(int) $recursive_subgroups][(int) $include_users] = $users;
            }
        }

        return $this->members_cache[(int) $this->get_id(
        )][(int) $include_subgroups][(int) $recursive_subgroups][(int) $include_users];
    }

    /**
     * Returns the condition to retrieve the members of the subgroups
     *
     * @param $include_subgroups boolean - Include the children of the subgroups
     * @param $recursive_subgroups boolean - Include the direct subgroups or include all the subgroups
     *
     * @return InCondition
     */
    private function get_members_condition($include_subgroups = false, $recursive_subgroups = false)
    {
        $groups = array();
        $groups[] = $this->get_id();

        if ($include_subgroups)
        {
            $subgroups = $this->get_children($recursive_subgroups);

            while ($subgroup = $subgroups->next_result())
            {
                $groups[] = $subgroup->get_id();
            }
        }

        return new InCondition(
            new PropertyConditionVariable(
                CourseGroupUserRelation::class, CourseGroupUserRelation::PROPERTY_COURSE_GROUP
            ), $groups
        );
    }

    /**
     * Gets the name of this course_group
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * Inherited method which specifies how to identify the tree this location is situated in.
     * Should be used as the
     * basic set of condition whenever one makes a query.
     */
    public function get_nested_set_condition_array()
    {
        $conditions = parent::get_nested_set_condition_array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($this->get_course_code())
        );

        return $conditions;
    }

    /**
     * Checks if a user is a member of this group
     *
     * @param $user User
     *
     * @return boolean
     */
    public function is_member($user)
    {
        return DataManager::is_course_group_member($this->get_id(), $user->get_id());
    }

    /**
     * Determines if the course group users were randomly subscribed
     *
     * @return boolean
     */
    public function is_random_registration_done()
    {
        return $this->get_default_property(self::PROPERTY_RANDOM_REG);
    }

    /**
     * Determines if self registration is allowed
     *
     * @return boolean
     */
    public function is_self_registration_allowed()
    {
        return $this->get_default_property(self::PROPERTY_SELF_REG);
    }

    /**
     * Determines if self unregistration is allowed
     *
     * @return boolean
     */
    public function is_self_unregistration_allowed()
    {
        return $this->get_default_property(self::PROPERTY_SELF_UNREG);
    }

    public function set_course_code($code)
    {
        $this->set_default_property(self::PROPERTY_COURSE_CODE, $code);
    }

    /**
     * Sets the description of this course_group
     *
     * @param $description string
     */
    public function set_description($description)
    {
        return $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    public function set_group_id($id)
    {
        return $this->set_default_property(self::PROPERTY_GROUP_ID, $id);
    }

    public function set_id($id)
    {
        return $this->set_default_property(self::PROPERTY_ID, $id);
    }

    /**
     * Sets the maximum number of child course groups that users can be subscribed to.
     *
     * @param $max_number_of_course_group_per_member int|null If null, no limit is set to the number of child course
     *        groups.
     */
    public function set_max_number_of_course_group_per_member($max_number_of_course_group_per_member)
    {
        return $this->set_default_property(
            self::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER, $max_number_of_course_group_per_member
        );
    }

    /**
     * Sets the maximum number of members of this course_group If the new value is smaller than the number of members
     * currently subscribed, no changes are made.
     *
     * @param $max_number_of_members int|null If null, no limit is set to the number of members.
     */
    public function set_max_number_of_members($max_number_of_members)
    {
        // Todo: Check current number of members.
        return $this->set_default_property(self::PROPERTY_MAX_NUMBER_OF_MEMBERS, $max_number_of_members);
    }

    /**
     * Sets the name of this course_group
     *
     * @param $name string
     */
    public function set_name($name)
    {
        return $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Sets if the course group users were randomly subscribed
     *
     * @param $self_reg boolean
     */
    public function set_random_registration_done($random_registration_done)
    {
        return $this->set_default_property(self::PROPERTY_RANDOM_REG, $random_registration_done);
    }

    /**
     * Sets if self registration is allowed
     *
     * @param $self_reg boolean
     */
    public function set_self_registration_allowed($self_reg)
    {
        if (is_null($self_reg))
        {
            $self_reg = 0;
        }

        return $this->set_default_property(self::PROPERTY_SELF_REG, $self_reg);
    }

    /**
     * Sets if self unregistration is allowed
     *
     * @param $self_unreg boolean
     */
    public function set_self_unregistration_allowed($self_unreg)
    {
        if (is_null($self_unreg))
        {
            $self_unreg = 0;
        }

        return $this->set_default_property(self::PROPERTY_SELF_UNREG, $self_unreg);
    }

    /**
     * Subscribes users to this course_group
     *
     * @param array|User A single user or an array of users
     */
    public function subscribe_users($users)
    {
        return DataManager::subscribe_users_to_course_groups($users, $this);
    }

    /**
     * Unsubscribes users from this course_group
     *
     * @param array|User A single user or an array of users
     */
    public function unsubscribe_users($users)
    {
        return DataManager::unsubscribe_users_from_course_groups($users, $this->get_id());
    }

    public function update()
    {
        if ($this->check_before_save())
        {
            return parent::update();
        }

        return false;
    }
}
