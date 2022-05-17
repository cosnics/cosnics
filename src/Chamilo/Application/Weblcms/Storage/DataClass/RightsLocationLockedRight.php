<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class defines that a right on a right location is locked
 *
 * @package application\weblcms;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RightsLocationLockedRight extends DataClass
{
    const PROPERTY_LOCATION_ID = 'location_id';
    const PROPERTY_RIGHT_ID = 'right_id';

    /**
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    static function get_default_property_names($extended_property_names = [])
    {
        $extended_property_names[] = self::PROPERTY_LOCATION_ID;
        $extended_property_names[] = self::PROPERTY_RIGHT_ID;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the location_id property of this object
     *
     * @return int
     */
    function get_location_id()
    {
        return $this->get_default_property(self::PROPERTY_LOCATION_ID);
    }

    /**
     * Returns the right_id property of this object
     *
     * @return int
     */
    function get_right_id()
    {
        return $this->get_default_property(self::PROPERTY_RIGHT_ID);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_rights_location_locked_right';
    }

    /**
     * Sets the location_id property of this object
     *
     * @param $location_id int
     */
    function set_location_id($location_id)
    {
        $this->set_default_property(self::PROPERTY_LOCATION_ID, $location_id);
    }

    /**
     * Sets the right_id property of this object
     *
     * @param $right_id int
     */
    function set_right_id($right_id)
    {
        $this->set_default_property(self::PROPERTY_RIGHT_ID, $right_id);
    }
}