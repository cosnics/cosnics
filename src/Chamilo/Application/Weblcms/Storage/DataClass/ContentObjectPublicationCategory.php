<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: content_object_publication_category.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.category_manager
 */
/**
 *
 * @author Sven Vanpoucke
 */
class ContentObjectPublicationCategory extends \Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory implements 
    \Chamilo\Configuration\Category\Interfaces\CategoryVisibilitySupported, DisplayOrderDataClassListenerSupport
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_COURSE = 'course_id';
    const PROPERTY_TOOL = 'tool';
    const PROPERTY_ALLOW_CHANGE = 'allow_change';
    const PROPERTY_VISIBLE = 'visible';

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent :: __construct($default_properties, $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    public function create($create_in_batch = false)
    {
        $succes = parent :: create();
        if (! $succes)
        {
            return false;
        }
        
        if ($this->get_parent())
        {
            $parent = WeblcmsRights :: get_instance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                WeblcmsRights :: TYPE_COURSE_CATEGORY, 
                $this->get_parent(), 
                $this->get_course());
        }
        else
        {
            $course_tool = DataManager :: retrieve_course_tool_by_name($this->get_tool());
            $course_tool_id = $course_tool->get_id();
            
            $parent = WeblcmsRights :: get_instance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                WeblcmsRights :: TYPE_COURSE_MODULE, 
                $course_tool_id, 
                $this->get_course());
        }
        
        return WeblcmsRights :: get_instance()->create_location_in_courses_subtree(
            WeblcmsRights :: TYPE_COURSE_CATEGORY, 
            $this->get_id(), 
            $parent, 
            $this->get_course(), 
            $create_in_batch);
    }

    public function create_dropbox($course_code)
    {
        $this->set_course($course_code);
        $this->set_tool('document');
        $this->set_name(Translation :: get('Dropbox'));
        $this->set_parent(0);
        $this->set_allow_change(0);
        
        $this->create();
    }

    public function update($move = false)
    {
        $succes = parent :: update();
        if (! $succes)
        {
            return false;
        }
        
        if ($move)
        {
            if ($this->get_parent())
            {
                $new_parent_id = WeblcmsRights :: get_instance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                    WeblcmsRights :: TYPE_COURSE_CATEGORY, 
                    $this->get_parent(), 
                    $this->get_course());
            }
            else
            {
                $course_module_id = DataManager :: retrieve_course_tool_by_name($this->get_tool())->get_id();
                $new_parent_id = WeblcmsRights :: get_instance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                    WeblcmsRights :: TYPE_COURSE_MODULE, 
                    $course_module_id, 
                    $this->get_course());
            }
            
            $location = WeblcmsRights :: get_instance()->get_weblcms_location_by_identifier_from_courses_subtree(
                WeblcmsRights :: TYPE_COURSE_CATEGORY, 
                $this->get_id(), 
                $this->get_course());
            
            if ($location)
            {
                return $location->move($new_parent_id);
            }
        }
        
        return true;
    }

    public function delete()
    {
        $location = WeblcmsRights :: get_instance()->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights :: TYPE_COURSE_CATEGORY, 
            $this->get_id(), 
            $this->get_course());
        if ($location)
        {
            if (! $location->delete())
            {
                return false;
            }
        }
        
        return parent :: delete();
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return array(
            self :: PROPERTY_COURSE, 
            self :: PROPERTY_ID, 
            self :: PROPERTY_NAME, 
            self :: PROPERTY_TOOL, 
            self :: PROPERTY_PARENT, 
            self :: PROPERTY_DISPLAY_ORDER, 
            self :: PROPERTY_ALLOW_CHANGE, 
            self :: PROPERTY_VISIBLE);
    }

    public function get_course()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE);
    }

    public function set_course($course)
    {
        $this->set_default_property(self :: PROPERTY_COURSE, $course);
    }

    public function get_tool()
    {
        return $this->get_default_property(self :: PROPERTY_TOOL);
    }

    public function set_tool($tool)
    {
        $this->set_default_property(self :: PROPERTY_TOOL, $tool);
    }

    public function get_allow_change()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOW_CHANGE);
    }

    public function set_allow_change($allow_change)
    {
        $this->set_default_property(self :: PROPERTY_ALLOW_CHANGE, $allow_change);
    }

    /**
     * Implementation of CategoryVisibilitySupported
     */
    /**
     *
     * @return True if the category is visible for everyone.
     */
    public function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE);
    }

    public function set_visibility($visibility)
    {
        $this->set_default_property(self :: PROPERTY_VISIBLE, $visibility);
    }

    public function toggle_visibility()
    {
        $this->set_visibility(! $this->get_visibility());
    }

    /**
     * Recursively checks the visibility of a category and its parent.
     * This is needed because when a category is
     * invisible, its children are not necessarily marked invisible too.
     */
    public function is_recursive_visible()
    {
        if ($this->get_visibility())
        {
            if ($this->get_parent() != 0)
            {
                $parent_category = DataManager :: retrieve_by_id(
                    ContentObjectPublicationCategory :: class_name(), 
                    $this->get_parent());
                
                return $parent_category->is_recursive_visible();
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @return string[string]
     *
     */
    protected function get_dependencies()
    {
        $id = $this->get_id();
        
        return array(
            ContentObjectPublicationCategory :: class_name() => new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory :: class_name(), 
                    ContentObjectPublicationCategory :: PROPERTY_PARENT), 
                new StaticConditionVariable($id)), 
            ContentObjectPublication :: class_name() => new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(), 
                    ContentObjectPublication :: PROPERTY_CATEGORY_ID), 
                new StaticConditionVariable($id)));
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
     * Returns the display order condition
     * 
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(
            new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_PARENT), 
            new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_COURSE), 
            new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_TOOL));
    }
}
