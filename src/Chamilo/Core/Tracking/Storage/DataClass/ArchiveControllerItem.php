<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * $Id: archive_controller_item.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * 
 * @package tracking.lib
 */

/**
 * This class presents a archive controller item
 * 
 * @author Sven Vanpoucke
 */
class ArchiveControllerItem extends DataClass
{
    /**
     * Tracker properties
     */
    const PROPERTY_ORIGINAL_TABLE = 'original_table';
    const PROPERTY_START_DATE = 'start_date';
    const PROPERTY_END_DATE = 'end_date';
    const PROPERTY_TABLE_NAME = 'table_name';

    /**
     * Get the default properties
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_ORIGINAL_TABLE, 
                self :: PROPERTY_START_DATE, 
                self :: PROPERTY_END_DATE, 
                self :: PROPERTY_TABLE_NAME));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     * Returns the original_table of this archive controller item.
     * 
     * @return the original_table.
     */
    public function get_original_table()
    {
        return $this->get_default_property(self :: PROPERTY_ORIGINAL_TABLE);
    }

    /**
     * Sets the original_table of this archive controller item.
     * 
     * @param original_table
     */
    public function set_original_table($original_table)
    {
        $this->set_default_property(self :: PROPERTY_ORIGINAL_TABLE, $original_table);
    }

    /**
     * Returns the start_date of this archive controller item.
     * 
     * @return the start_date.
     */
    public function get_start_date()
    {
        return $this->get_default_property(self :: PROPERTY_START_DATE);
    }

    /**
     * Sets the start_date of this archive controller item.
     * 
     * @param start_date
     */
    public function set_start_date($start_date)
    {
        $this->set_default_property(self :: PROPERTY_START_DATE, $start_date);
    }

    /**
     * Returns the end_date of this Tracker.
     * 
     * @return the end_date.
     */
    public function get_end_date()
    {
        return $this->get_default_property(self :: PROPERTY_END_DATE);
    }

    /**
     * Sets the end_date of this Tracker.
     * 
     * @param end_date
     */
    public function set_end_date($end_date)
    {
        $this->set_default_property(self :: PROPERTY_END_DATE, $end_date);
    }

    /**
     * Returns the table_name of this Tracker.
     * 
     * @return the table_name.
     */
    public function get_table_name()
    {
        return DataManager :: PREFIX . $this->get_default_property(self :: PROPERTY_TABLE_NAME);
    }

    /**
     * Sets the table_name of this Tracker.
     * 
     * @param table_name
     */
    public function set_table_name($table_name)
    {
        $this->set_default_property(self :: PROPERTY_TABLE_NAME, $table_name);
    }
}
