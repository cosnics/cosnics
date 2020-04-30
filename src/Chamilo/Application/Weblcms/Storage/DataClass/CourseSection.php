<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 * This class defines a course section in which tools can be arranged
 * 
 * @package application\weblcms;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseSection extends DataClass implements DisplayOrderDataClassListenerSupport
{
    
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_VISIBLE = 'visible';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    
    /**
     * **************************************************************************************************************
     * Type Definitions *
     * **************************************************************************************************************
     */
    const TYPE_DISABLED = '0';
    const TYPE_DISABLED_NAME = 'disabled';
    const TYPE_TOOL = 1;
    const TYPE_TOOL_NAME = 'tool';
    const TYPE_LINK = 2;
    const TYPE_LINK_NAME = 'link';
    const TYPE_ADMIN = 3;
    const TYPE_ADMIN_NAME = 'admin';
    const TYPE_CUSTOM = 4;
    const TYPE_CUSTOM_NAME = 'custom';

    private static $type_name_mapping = array(
        self::TYPE_DISABLED => self::TYPE_DISABLED_NAME, 
        self::TYPE_TOOL => self::TYPE_TOOL_NAME, 
        self::TYPE_LINK => self::TYPE_LINK_NAME, 
        self::TYPE_ADMIN => self::TYPE_ADMIN_NAME, 
        self::TYPE_CUSTOM => self::TYPE_CUSTOM_NAME);

    private $displayName;

    /**
     *
     * @param string[] $default_properties
     * @param string[] $optional_properties
     */
    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent::__construct($default_properties = $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    /**
     * **************************************************************************************************************
     * Type Mapping Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the type from the given type name
     * 
     * @param $type_name String
     *
     * @return int
     */
    public static function get_type_from_type_name($type_name)
    {
        $type = array_search($type_name, self::$type_name_mapping);
        
        if (! $type)
        {
            throw new Exception(Translation::get('CouldNotFindSectionTypeName', array('TYPE_NAME' => $type_name)));
        }
        
        return $type;
    }

    /**
     * Returns the type name from a given type
     * 
     * @param $type int
     *
     * @return String
     */
    public static function get_type_name_from_type($type)
    {
        if (! array_key_exists($type, self::$type_name_mapping))
        {
            throw new Exception(Translation::get('CouldNotFindSectionType', array('TYPE' => $type)));
        }
        
        return self::$type_name_mapping[$type];
    }

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the default properties of this dataclass
     * 
     * @return String[] - The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_COURSE_ID, 
                self::PROPERTY_NAME, 
                self::PROPERTY_TYPE, 
                self::PROPERTY_VISIBLE, 
                self::PROPERTY_DISPLAY_ORDER));
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
            CourseToolRelCourseSection::class => new EqualityCondition(
                new PropertyConditionVariable(
                    CourseToolRelCourseSection::class,
                    CourseToolRelCourseSection::PROPERTY_SECTION_ID), 
                new StaticConditionVariable($id)));
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course_id property of this object
     * 
     * @return String
     */
    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     * Sets the course_id property of this object
     * 
     * @param $course_id String
     */
    public function set_course_id($course_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }

    /**
     * Returns the name property of this object
     * 
     * @return String
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     *
     * @return string
     */
    public function getDisplayName()
    {
        if (! isset($this->displayName))
        {
            if ($this->get_type() == CourseSection::TYPE_CUSTOM)
            {
                $this->displayName = $this->get_name();
            }
            else
            {
                $this->displayName = Translation::get($this->get_name());
            }
        }
        
        return $this->displayName;
    }

    /**
     * Sets the name property of this object
     * 
     * @param $name String
     */
    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Returns the type property of this object
     * 
     * @return int
     */
    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    /**
     * Sets the type property of this object
     * 
     * @param $type int
     */
    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    /**
     * Returns the tool_id property of this object
     * 
     * @return boolean
     */
    public function is_visible()
    {
        return $this->get_default_property(self::PROPERTY_VISIBLE);
    }

    /**
     * Sets the visible property of this object
     * 
     * @param $visible boolean
     */
    public function set_visible($visible)
    {
        $this->set_default_property(self::PROPERTY_VISIBLE, $visible);
    }

    /**
     * Returns the display_order property of this object
     * 
     * @return int
     */
    public function get_display_order()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Sets the display_order property of this object
     * 
     * @param $display_order int
     */
    public function set_display_order($display_order)
    {
        $this->set_default_property(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    /**
     * Returns the property for the display order
     * 
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self::class, self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     * 
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self::class, self::PROPERTY_COURSE_ID));
    }
}
