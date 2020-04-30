<?php
namespace Chamilo\Application\Weblcms\Rights;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocationLockedRight;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Extension on the weblcms rights to define rights utilities for course management (courses / course types)
 * 
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CourseManagementRights extends WeblcmsRights
{
    /**
     * **************************************************************************************************************
     * Rights Definitions *
     * **************************************************************************************************************
     */
    const CREATE_COURSE_RIGHT = 1;
    const REQUEST_COURSE_RIGHT = 2;
    const DIRECT_SUBSCRIBE_RIGHT = 3;
    const TEACHER_DIRECT_SUBSCRIBE_RIGHT = 4;
    const REQUEST_SUBSCRIBE_RIGHT = 5;
    const TEACHER_REQUEST_SUBSCRIBE_RIGHT = 6;
    const CODE_SUBSCRIBE_RIGHT = 7;
    const DIRECT_UNSUBSCRIBE_RIGHT = 8;
    const TEACHER_UNSUBSCRIBE_RIGHT = 9;
    const PUBLISH_FROM_REPOSITORY_RIGHT = 10;
    const CAN_CHANGE_COURSE_TITLE_RIGHT = 11;
    const CAN_CHANGE_COURSE_TYPE_RIGHT = 12;
    const CAN_CHANGE_COURSE_VISUAL_CODE_RIGHT = 13;
    
    /**
     * **************************************************************************************************************
     * Definitions for the rights creation array *
     * **************************************************************************************************************
     */
    const PARAM_RIGHT_OPTION = 'right_option';
    const PARAM_RIGHT_LOCKED = 'right_locked';
    const PARAM_RIGHT_TARGETS = 'right_target';
    
    /**
     * **************************************************************************************************************
     * Right Options Definition *
     * **************************************************************************************************************
     */
    const RIGHT_OPTION_NOBODY = 1;
    const RIGHT_OPTION_ALL = 2;
    const RIGHT_OTPION_ME = 3;
    const RIGHT_OPTION_SELECT = 4;

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    private $rights;

    /**
     * Singleton
     * 
     * @var CourseManagementRights
     */
    private static $instance;

    /**
     * **************************************************************************************************************
     * Main Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Singleton
     * 
     * @return CourseManagementRights
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Checks if the given course management right is allowed on the given identifier and type for the given user
     * 
     * @param $right_id int
     * @param $identifier int
     * @param $type int - [OPTIONAL] default: self :: TYPE_COURSE
     * @param $user_id int - [OPTIONAL] default: null
     * @return boolean
     */
    public function is_allowed($right_id, $identifier, $type = self :: TYPE_COURSE, $user_id = null)
    {
        $entities = array();
        $entities[UserEntity::ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity::ENTITY_TYPE] = new PlatformGroupEntity();
        
        return RightsUtil::is_allowed(
            $right_id, 
            Manager::context(), 
            $user_id, 
            $entities, 
            $identifier, 
            $type, 
            0, 
            WeblcmsRights::TREE_TYPE_COURSE);
    }

    /**
     * Checks if the given course management right is allowed on the given identifier and type for the given platform
     * group
     * 
     * @author Anthony Hurst (Hogeschool Gent)
     * @param $right_id int The right to be checked.
     * @param $group_id int The group id.
     * @param $course_id int The course id.
     * @return boolean
     */
    public function is_allowed_for_platform_group($right_id, $group_id, $course_id)
    {
        $entity_type = PlatformGroupEntity::ENTITY_TYPE;
        
        if (! $this->rights[$entity_type][$group_id])
        {
            $base_group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class, $group_id);
            $location = CourseDataManager::retrieve_by_id(Course::class_name(), $course_id)->get_rights_location();
            
            if (is_null($base_group))
            {
                return false;
            }
            
            $groups = $base_group->get_parents(true)->as_array();
            $group_ids = array();
            foreach ($groups as $group)
            {
                $group_ids[$group->get_id()] = $group->get_id();
            }
            
            $conditions = array();
            $group_conditions = array();
            $group_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class_name(), 
                    RightsLocationEntityRight::PROPERTY_ENTITY_TYPE), 
                new StaticConditionVariable($entity_type));
            $group_conditions[] = new InCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class_name(), 
                    RightsLocationEntityRight::PROPERTY_ENTITY_ID), 
                $group_ids);
            $conditions[] = new AndCondition($group_conditions);
            $all_conditions = array();
            $all_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class_name(), 
                    RightsLocationEntityRight::PROPERTY_ENTITY_TYPE), 
                new StaticConditionVariable(0));
            $all_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class_name(), 
                    RightsLocationEntityRight::PROPERTY_ENTITY_ID), 
                new StaticConditionVariable(0));
            $conditions[] = new AndCondition($all_conditions);
            $condition = new OrCondition($conditions);
            $rights = \Chamilo\Core\Rights\Storage\DataManager::retrieve_granted_rights_array($location, $condition);
            $this->rights[$entity_type][$group_id] = $rights;
        }
        return array_search($right_id, $this->rights[$entity_type][$group_id]) !== false;
    }

    /**
     * **************************************************************************************************************
     * Metadata Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the available rights for the course management
     * 
     * @param $rights int[string] - extra rights
     * @return int[string]
     */
    public function get_base_course_management_rights($rights = array())
    {
        $rights[Translation::get('DirectSubscribeRight')] = self::DIRECT_SUBSCRIBE_RIGHT;
        $rights[Translation::get('CodeSubscribeRight')] = self::CODE_SUBSCRIBE_RIGHT;
        $rights[Translation::get('DirectUnsubscribeRight')] = self::DIRECT_UNSUBSCRIBE_RIGHT;
        
        return $rights;
    }

    /**
     * Returns the specific rights for the root and the course types (not for courses)
     * 
     * @param int[string] $rights
     *
     * @return int[string]
     */
    public function get_specific_course_management_rights($rights = array())
    {
        $rights[Translation::get('CreateCourseRight')] = self::CREATE_COURSE_RIGHT;
        $rights[Translation::get('RequestCourseRight')] = self::REQUEST_COURSE_RIGHT;
        $rights[Translation::get('TeacherDirectSubscribeRight')] = self::TEACHER_DIRECT_SUBSCRIBE_RIGHT;
        $rights[Translation::get('TeacherRequestSubscribeRight')] = self::TEACHER_REQUEST_SUBSCRIBE_RIGHT;
        $rights[Translation::get('TeacherUnsubscribeRight')] = self::TEACHER_UNSUBSCRIBE_RIGHT;
        $rights[Translation::get('PublishFromRepositoryRight')] = self::PUBLISH_FROM_REPOSITORY_RIGHT;
        $rights[Translation::get('CanChangeCourseTypeRight')] = self::CAN_CHANGE_COURSE_TYPE_RIGHT;
        $rights[Translation::get('CanChangeCourseTitleRight')] = self::CAN_CHANGE_COURSE_TITLE_RIGHT;
        $rights[Translation::get('CanChangeCourseVisualCodeRight')] = self::CAN_CHANGE_COURSE_VISUAL_CODE_RIGHT;
        
        return $rights;
    }

    /**
     * Returns all the course management rights
     * 
     * @return int[string]
     */
    public function get_all_course_management_rights()
    {
        $rights = $this->get_specific_course_management_rights();
        return $this->get_base_course_management_rights($rights);
    }

    /**
     * **************************************************************************************************************
     * CRUD Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Create the rights for given from values
     * 
     * @param $base_object DataClass
     * @param string[string] values - the values
     * @param $form_values string[string]
     */
    public function create_rights_from_values(DataClass $base_object, $values)
    {
        $available_rights = $this->get_all_course_management_rights();
        $location = $base_object->get_rights_location();
        
        if (is_null($location))
        {
            return false;
        }
        
        $location_id = $location->get_id();
        
        $location->clear_rights();
        DataManager::clear_locked_rights_for_location($location_id);
        
        $succes = true;
        
        foreach ($available_rights as $right_id)
        {
            if (! array_key_exists(self::PARAM_RIGHT_OPTION, $values) ||
                 ! array_key_exists($right_id, $values[self::PARAM_RIGHT_OPTION]) ||
                 ! $base_object->can_change_course_management_right($right_id))
            {
                $parent_location = $location->get_parent();
                if ($parent_location)
                {
                    $parent_location->set_context($location->get_context());
                    $this->copy_rights_to_child_location($parent_location, $location, $right_id);
                }
            }
            
            $option = $values[self::PARAM_RIGHT_OPTION][$right_id];
            
            switch ($option)
            {
                case self::RIGHT_OPTION_ALL :
                    $succes &= $this->invert_location_entity_right($right_id, 0, 0, $location_id);
                    break;
                case self::RIGHT_OTPION_ME :
                    $succes &= $this->invert_location_entity_right($right_id, Session::get_user_id(), 1, $location_id);
                    break;
                case self::RIGHT_OPTION_SELECT :
                    if (! array_key_exists(self::PARAM_RIGHT_TARGETS, $values) ||
                         ! array_key_exists($right_id, $values[self::PARAM_RIGHT_TARGETS]))
                    {
                        continue;
                    }
                    
                    foreach ($values[self::PARAM_RIGHT_TARGETS][$right_id] as $entity_type => $target_ids)
                    {
                        foreach ($target_ids as $target_id)
                        {
                            $succes &= $this->invert_location_entity_right(
                                $right_id, 
                                $target_id, 
                                $entity_type, 
                                $location_id);
                        }
                    }
            }
            
            if (array_key_exists(self::PARAM_RIGHT_LOCKED, $values) &&
                 array_key_exists($right_id, $values[self::PARAM_RIGHT_LOCKED]))
            {
                $rights_location_locked_right = new RightsLocationLockedRight();
                $rights_location_locked_right->set_location_id($location_id);
                $rights_location_locked_right->set_right_id($right_id);
                
                $succes &= $rights_location_locked_right->create();
                
                $succes &= $this->copy_rights_to_child_locations($location, $right_id);
            }
        }
        
        return $succes;
    }

    /**
     * Copies the rights from a parent location to it's chidren
     * 
     * @param $location RightsLocation
     * @param $right_id int
     *
     * @return boolean
     */
    public function copy_rights_to_child_locations(RightsLocation $location, $right_id = null)
    {
        if (! $location)
        {
            return true;
        }
        
        $succes = true;
        
        $location_children = $location->get_children();
        while ($child = $location_children->next_result())
        {
            $child->set_context($location->get_context());
            
            if (! $this->copy_rights_to_child_location($location, $child, $right_id))
            {
                $succes = false;
            }
        }
        
        return $succes;
    }

    /**
     * Copies the rights from a parent to a child location
     * 
     * @param $parent_location RightsLocation
     * @param $child_location RightsLocation
     * @param $right_id int
     *
     * @return boolean
     */
    public function copy_rights_to_child_location(RightsLocation $parent_location, RightsLocation $child_location, 
        $right_id = null)
    {
        $succes = true;
        
        $current_rights = $parent_location->get_rights_entities($right_id);
        
        if (! is_null($right_id))
        {
            $child_location->clear_right($right_id);
        }
        else
        {
            $child_location->clear_rights();
        }
        
        foreach ($current_rights as $current_right)
        {
            $current_right->set_location_id($child_location->get_id());
            if (! $current_right->create())
            {
                $succes = false;
            }
        }
        
        return $succes;
    }

    /**
     * **************************************************************************************************************
     * Retrieve Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Retrieves the rights for a given location
     * 
     * @param $location RightsLocation
     */
    public function retrieve_rights_location_rights_for_location(RightsLocation $location, $available_rights = array())
    {
        return \Chamilo\Core\Rights\Storage\DataManager::retrieve_rights_location_rights_for_location(
            Manager::context(), 
            $location->get_id(), 
            $available_rights);
    }

    /**
     * Returns wheter or not the given right is locked for the given base object
     * 
     * @param $base_object DataClass
     * @param $right_id int return boolean
     */
    public function is_right_locked_for_base_object(DataClass $base_object, $right_id)
    {
        $location = $base_object->get_rights_location();
        
        if (! $location)
        {
            return false;
        }
        
        return DataManager::is_right_locked_for_location($location->get_id(), $right_id);
    }
}
