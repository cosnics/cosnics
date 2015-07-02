<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * $Id: tracker_setting.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * 
 * @package tracking.lib
 */

/**
 * This class presents a tracker_setting
 * 
 * @author Sven Vanpoucke
 */
class TrackerSetting extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * TrackerSetting properties
     */
    const PROPERTY_TRACKER_ID = 'tracker_id';
    const PROPERTY_SETTING = 'setting';
    const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties
     * 
     * @return array The property names.
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_TRACKER_ID, self :: PROPERTY_SETTING, self :: PROPERTY_VALUE));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     * Returns the tracker_id of this TrackerSetting.
     * 
     * @return the tracker_id.
     */
    public function get_tracker_id()
    {
        return $this->get_default_property(self :: PROPERTY_TRACKER_ID);
    }

    /**
     * Sets the tracker_id of this TrackerSetting.
     * 
     * @param tracker_id
     */
    public function set_tracker_id($tracker_id)
    {
        $this->set_default_property(self :: PROPERTY_TRACKER_ID, $tracker_id);
    }

    /**
     * Returns the setting of this TrackerSetting.
     * 
     * @return the setting.
     */
    public function get_setting()
    {
        return $this->get_default_property(self :: PROPERTY_SETTING);
    }

    /**
     * Sets the setting of this TrackerSetting.
     * 
     * @param setting
     */
    public function set_setting($setting)
    {
        $this->set_default_property(self :: PROPERTY_SETTING, $setting);
    }

    /**
     * Returns the value of this TrackerSetting.
     * 
     * @return the value.
     */
    public function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value of this TrackerSetting.
     * 
     * @param value
     */
    public function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }
}
