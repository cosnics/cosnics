<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Abstract class that describes a value for a course setting
 *
 * @package application\weblcms;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CourseSettingValue extends DataClass
{
    const PROPERTY_VALUE = 'value';

    /**
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_VALUE;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the value of this CourseSettingValue object
     *
     * @return String
     */
    function get_value()
    {
        return $this->get_default_property(self::PROPERTY_VALUE);
    }

    /**
     * Sets the value of this CourseSettingValue object
     *
     * @param $value String
     */
    function set_value($value)
    {
        $this->set_default_property(self::PROPERTY_VALUE, $value);
    }
}

?>