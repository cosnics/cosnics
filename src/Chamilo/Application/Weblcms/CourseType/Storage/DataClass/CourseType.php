<?php
namespace Chamilo\Application\Weblcms\CourseType\Storage\DataClass;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Describes a course type (templating and rights system for courses)
 * 
 * @package application\weblcms\course_type;
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseType extends DataClass implements DisplayOrderDataClassListenerSupport
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_ACTIVE = 'active';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param mixed[string] $default_properties
     * @param mixed[string] $optional_properties
     */
    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent::__construct($default_properties, $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    /**
     * Returns the default properties of this dataclass
     * 
     * @param string[] $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_TITLE, 
                self::PROPERTY_ACTIVE, 
                self::PROPERTY_DESCRIPTION, 
                self::PROPERTY_DISPLAY_ORDER));
    }

    /**
     * **************************************************************************************************************
     * CRUD Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Sets the default display order and creates this object in the database
     * 
     * @param bool $create_in_batch
     *
     * @return boolean
     */
    public function create($create_in_batch = false)
    {
        if (! parent::create())
        {
            return false;
        }
        
        $parent_id = $this->get_parent_rights_location()->get_id();
        
        if (! CourseManagementRights::getInstance()->create_location_in_courses_subtree(
            CourseManagementRights::TYPE_COURSE_TYPE, 
            $this->get_id(), 
            $parent_id, 
            0, 
            $create_in_batch, 
            0))
        {
            return false;
        }
        
        return true;
    }

    /**
     * Deletes the object and fixes the display orders
     * 
     * @return boolean
     */
    public function delete()
    {
        $location = $this->get_rights_location();
        
        if ($location)
        {
            if (! $location->delete())
            {
                return false;
            }
        }
        
        return parent::delete();
    }

    /**
     * **************************************************************************************************************
     * Rights Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the rights location for this object
     * 
     * @return RightsLocation
     */
    public function get_rights_location()
    {
        return CourseManagementRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
            CourseManagementRights::TYPE_COURSE_TYPE, 
            $this->get_id(), 
            0);
    }

    /**
     * Returns the (possible) parent rights location depending on this objects properties
     * 
     * @return RightsLocation
     */
    public function get_parent_rights_location()
    {
        return CourseManagementRights::getInstance()->get_courses_subtree_root(0);
    }

    /**
     * Returns the available rights for the course management
     * 
     * @return int[string]
     */
    public function get_available_course_management_rights()
    {
        return CourseManagementRights::getInstance()->get_all_course_management_rights();
    }

    /**
     * Checks whether or not this object can change the given course management right Returns always true because a
     * course type is not limited
     * 
     * @param $right_id int
     *
     * @return boolean
     */
    public function can_change_course_management_right($right_id)
    {
        return true;
    }

    /**
     * Forces all the course rights to the courses connected to this course type.
     * 
     * @return boolean
     */
    public function force_rights_to_courses()
    {
        return CourseManagementRights::getInstance()->copy_rights_to_child_locations($this->get_rights_location());
    }

    /**
     * **************************************************************************************************************
     * Course Settings Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Retrieves course setting values for the given setting name and tool id
     * 
     * @param $setting_name string
     * @param $tool_id int
     *
     * @return string[]
     */
    public function get_course_setting($setting_name, $tool_id = 0)
    {
        return CourseSettingsController::getInstance()->get_course_type_setting(
            $this->get_id(), 
            $setting_name, 
            $tool_id);
    }

    /**
     * Retrieves the default values for a given course setting
     * 
     * @param $setting_name string
     * @param $tool_id int
     *
     * @return string[]
     */
    public function get_default_course_setting($setting_name, $tool_id)
    {
        return CourseSettingsController::getInstance()->get_default_setting($setting_name, $tool_id);
    }

    /**
     * Delegation function to create course settings from given values
     * 
     * @param $values string[]
     *
     * @return boolean
     */
    public function create_course_settings_from_values($values)
    {
        return CourseSettingsController::getInstance()->handle_settings_for_object_with_given_values($this, $values);
    }

    /**
     * Delegation function to update course settings from given values
     * 
     * @param $values string
     *
     * @return boolean
     */
    public function update_course_settings_from_values($values)
    {
        return CourseSettingsController::getInstance()->handle_settings_for_object_with_given_values(
            $this, 
            $values, 
            CourseSettingsController::SETTING_ACTION_UPDATE);
    }

    /**
     * Creates a course setting relation for the given course setting object
     * 
     * @param mixed[string] CourseSetting
     * @param $locked boolean
     *
     * @throws \Exception
     *
     * @return CourseTypeRelCourseSetting
     */
    public function create_course_setting_relation($course_setting, $locked)
    {
        $course_type_rel_setting = new CourseTypeRelCourseSetting();
        $course_type_rel_setting->set_course_setting_id($course_setting[CourseSetting::PROPERTY_ID]);
        $course_type_rel_setting->set_course_type($this);
        $course_type_rel_setting->set_locked($locked);
        
        if (! $course_type_rel_setting->create())
        {
            throw new \Exception(Translation::get('CouldNotCreateCourseRelCourseTypeSetting'));
        }
        
        return $course_type_rel_setting;
    }

    /**
     * Updates a course setting relation for the given course setting object
     * 
     * @param mixed[string] $course_setting
     * @param bool $locked
     *
     * @throws \Exception
     *
     * @return CourseTypeRelCourseSetting
     */
    public function update_course_setting_relation($course_setting, $locked)
    {
        $course_setting_relation = $this->retrieve_course_setting_relation($course_setting);
        
        if ($course_setting_relation && $course_setting_relation->is_locked() != $locked)
        {
            $course_setting_relation->set_locked($locked);
            if (! $course_setting_relation->update())
            {
                throw new \Exception(Translation::get('CouldNotUpdateCourseRelCourseTypeSetting'));
            }
        }
        
        return $course_setting_relation;
    }

    /**
     * Retrieves a course setting relation object for the given course setting object
     * 
     * @param mixed[string] CourseSetting
     * @return CourseTypeRelCourseSetting
     */
    public function retrieve_course_setting_relation($course_setting)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeRelCourseSetting::class_name(), 
                CourseTypeRelCourseSetting::PROPERTY_COURSE_SETTING_ID), 
            new StaticConditionVariable($course_setting[CourseSetting::PROPERTY_ID]));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeRelCourseSetting::class_name(), 
                CourseTypeRelCourseSetting::PROPERTY_COURSE_TYPE_ID), 
            new StaticConditionVariable($this->get_id()));
        
        $condition = new AndCondition($conditions);
        
        return DataManager::retrieve(
            CourseTypeRelCourseSetting::class_name(), 
            new DataClassRetrieveParameters($condition));
    }

    /**
     * Returns whether or not a given course setting is locked for this object.
     * 
     * @param mixed[string] $course_setting
     *
     * @return boolean
     */
    public function is_course_setting_locked($course_setting)
    {
        $course_setting_relation = $this->retrieve_course_setting_relation($course_setting);
        
        if (! $course_setting_relation)
        {
            return false;
        }
        
        return $course_setting_relation->is_locked();
    }

    /**
     * Returns whether or not a given course setting can be changed by this object
     * 
     * @param $course_setting CourseSetting
     *
     * @return boolean
     */
    public function can_change_course_setting($course_setting)
    {
        return true;
    }

    /**
     * Forces all the course settings to the courses connected to this course type.
     * Handles the copied settings for the
     * course table
     * 
     * @return boolean
     */
    public function force_course_settings_to_courses()
    {
        CourseSettingsController::getInstance()->clear_cache_for_type_and_object(
            CourseSettingsController::SETTING_TYPE_COURSE_TYPE, 
            $this->get_id());
        
        if (! \Chamilo\Application\Weblcms\Course\Storage\DataManager::copy_course_settings_from_course_type(
            $this->get_id()))
        {
            return false;
        }
        
        $properties = new DataClassProperties();
        
        $copied_settings = CourseSettingsConnector::get_copied_settings_for_course();
        foreach ($copied_settings as $setting_name => $property)
        {
            $properties->add(
                new DataClassProperty(
                    new PropertyConditionVariable(Course::class_name(), $property), 
                    new StaticConditionVariable($this->get_course_setting($setting_name))));
        }
        
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::update_courses_from_course_type_with_properties(
            $this->get_id(), 
            $properties);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Updates the current display order added with the given value
     * 
     * @param $count int
     */
    public function update_display_order_with_count($count)
    {
        $this->set_display_order($this->get_display_order() + $count);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the title of this CourseType object
     * 
     * @return String
     */
    public function get_title()
    {
        return $this->get_default_property(self::PROPERTY_TITLE);
    }

    /**
     * Sets the title of this CourseType object
     * 
     * @param $title String
     */
    public function set_title($title)
    {
        $this->set_default_property(self::PROPERTY_TITLE, $title);
    }

    /**
     * Returns the description of this CourseType object
     * 
     * @return String
     */
    public function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this CourseType object
     * 
     * @param $description String
     */
    public function set_description($description)
    {
        $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the active status of this CourseType object
     * 
     * @return boolean
     */
    public function is_active()
    {
        return $this->get_default_property(self::PROPERTY_ACTIVE);
    }

    /**
     * Sets the active status of this CourseType object
     * 
     * @param $active boolean
     */
    public function set_active($active)
    {
        $this->set_default_property(self::PROPERTY_ACTIVE, $active);
    }

    /**
     * Returns the display_order of this CourseType object
     * 
     * @return int
     */
    public function get_display_order()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Sets the display_order of this CourseType object
     * 
     * @param $display_order int
     */
    public function set_display_order($display_order)
    {
        $this->set_default_property(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    /**
     * **************************************************************************************************************
     * Display Order Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the property for the display order
     * 
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self::class_name(), self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     * 
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array();
    }
}
