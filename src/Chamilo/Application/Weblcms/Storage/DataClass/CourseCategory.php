<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.category_manager
 */
/**
 *
 * @author Sven Vanpoucke
 */
class CourseCategory extends PlatformCategory implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_CODE = 'code';
    const PROPERTY_STATE = 'state';
    const STATE_ARCHIVE = 0;

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent::__construct($default_properties = $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    public function get_code()
    {
        return $this->get_default_property(self::PROPERTY_CODE);
    }

    public function set_code($code)
    {
        $this->set_default_property(self::PROPERTY_CODE, $code);
    }

    public function get_state()
    {
        return $this->get_default_property(self::PROPERTY_STATE);
    }

    public function set_state($state)
    {
        $this->set_default_property(self::PROPERTY_STATE, $state);
    }

    /**
     * Get the default properties of all contributions.
     *
     * @return array The property titles.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_CODE, self::PROPERTY_STATE));
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
        if (! parent::delete())
        {
            return false;
        }

        $parent_variable = new PropertyConditionVariable(CourseCategory::class, CourseCategory::PROPERTY_PARENT);

        $condition = new EqualityCondition($parent_variable, new StaticConditionVariable($this->get_id()));

        $properties = new DataClassProperties();
        $properties->add(new DataClassProperty($parent_variable, new StaticConditionVariable($this->get_parent())));

        if (! DataManager::updates(CourseCategory::class, $properties, $condition))
        {
            return false;
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Course::class, Course::PROPERTY_CATEGORY_ID),
            new StaticConditionVariable($this->get_id()));

        $properties = new DataClassProperties();
        $properties->add(
            new DataClassProperty(
                new PropertyConditionVariable(Course::class, Course::PROPERTY_CATEGORY_ID),
                new StaticConditionVariable($this->get_parent())));

        if (! DataManager::updates(Course::class, $properties, $condition))
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
        return new PropertyConditionVariable(self::class, self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     *
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self::class, self::PROPERTY_PARENT));
    }

    public function get_parent_ids()
    {
        if ($this->get_parent() == 0)
        {
            return array();
        }
        else
        {
            $parent = DataManager::retrieve_by_id(CourseCategory::class, $this->get_parent());

            $parent_ids = array();
            $parent_ids[] = $parent->get_id();
            $parent_ids = array_merge($parent_ids, $parent->get_parent_ids());
            return $parent_ids;
        }
    }

    public function get_fully_qualified_name($include_self = true)
    {
        $parent_ids = $this->get_parent_ids();
        $names = array();

        if ($include_self)
        {
            $names[] = $this->get_name();
        }

        foreach ($parent_ids as $parent_id)
        {
            $parent = DataManager::retrieve_by_id(CourseCategory::class, $parent_id);
            $names[] = $parent->get_name();
        }

        return implode(' <span class="text-primary">></span> ', array_reverse($names));
    }

    public function get_children_ids($recursive = true)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_PARENT),
            new StaticConditionVariable($this->get_id()));

        if (! $recursive)
        {
            $parameters = new DataClassDistinctParameters(
                $condition,
                new DataClassProperties(array(new PropertyConditionVariable(self::class, self::PROPERTY_ID))));
            return DataManager::distinct(self::class, $parameters);
        }
        else
        {
            $children_ids = array();
            $children = DataManager::retrieve_categories($condition);

            foreach($children as $child)
            {
                $children_ids[] = $child->get_id();
                $children_ids = array_merge($children_ids, $child->get_children_ids($recursive));
            }

            return $children_ids;
        }
    }
}
