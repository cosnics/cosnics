<?php
namespace Chamilo\Configuration\Category\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.common.category_manager
 */
abstract class PlatformCategory extends DataClass
{
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    const PROPERTY_NAME = 'name';

    const PROPERTY_PARENT = 'parent_id';

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return null;
    }

    /**
     * Get the default properties of all contributions.
     *
     * @return array The property titles.
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_NAME, self::PROPERTY_PARENT, self::PROPERTY_DISPLAY_ORDER)
        );
    }

    /**
     * Returns the dependencies for this dataclass
     *
     * @return string[string]
     *
     */
    protected function get_dependencies()
    {
        return array(
            $this->class_name() => new EqualityCondition(
                new PropertyConditionVariable($this->class_name(), self::PROPERTY_PARENT),
                new StaticConditionVariable($this->get_id())
            )
        );
    }

    public function get_display_order()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER);
    }

    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    public function get_parent()
    {
        return $this->get_default_property(self::PROPERTY_PARENT);
    }

    public function set_display_order($display_order)
    {
        $this->set_default_property(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    public function set_parent($parent)
    {
        $this->set_default_property(self::PROPERTY_PARENT, $parent);
    }

    public function update($move = false)
    {
        return parent::update();
    }
}
