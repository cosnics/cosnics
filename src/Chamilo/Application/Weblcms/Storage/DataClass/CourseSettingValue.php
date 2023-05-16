<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Abstract class that describes a value for a course setting
 *
 * @package application\weblcms;
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class CourseSettingValue extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_VALUE = 'value';

    /**
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_VALUE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
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
    public function get_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    /**
     * Sets the value of this CourseSettingValue object
     *
     * @param $value String
     */
    public function set_value($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);
    }
}