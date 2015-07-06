<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: course_category.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.category_manager
 */
/**
 *
 * @author Sven Vanpoucke
 */
class CourseCategory extends PlatformCategory implements DisplayOrderDataClassListenerSupport
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_CODE = 'code';

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent :: __construct($default_properties = $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    public function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    public function set_code($code)
    {
        $this->set_default_property(self :: PROPERTY_CODE, $code);
    }

    /**
     * Get the default properties of all contributions.
     * 
     * @return array The property titles.
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CODE));
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @return string[string]
     *
     */
    protected function get_dependencies()
    {
        return array();
    }

    /**
     * Deletes the dataclass in the database and updates the children and courses
     * 
     * @return bool
     */
    public function delete()
    {
        if (! parent :: delete())
        {
            return false;
        }
        
        $parent_variable = new PropertyConditionVariable(
            CourseCategory :: class_name(), 
            CourseCategory :: PROPERTY_PARENT);
        
        $condition = new EqualityCondition($parent_variable, new StaticConditionVariable($this->get_id()));
        
        $properties = new DataClassProperties();
        $properties->add(new DataClassProperty($parent_variable, new StaticConditionVariable($this->get_parent())));
        
        if (! DataManager :: updates(CourseCategory :: class_name(), $properties, $condition))
        {
            return false;
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_CATEGORY_ID), 
            new StaticConditionVariable($this->get_id()));
        
        $properties = new DataClassProperties();
        $properties->add(
            new DataClassProperty(
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_CATEGORY_ID), 
                new StaticConditionVariable($this->get_parent())));
        
        if (! DataManager :: updates(Course :: class_name(), $properties, $condition))
        {
            return false;
        }
        
        return true;
    }

    /**
     * Returns the property for the display order
     * 
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     * 
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_PARENT));
    }
}
