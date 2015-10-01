<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * $Id: home_block_config.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib
 */
class BlockConfiguration extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_BLOCK_ID = 'block_id';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties of all user course categories.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_BLOCK_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    public function get_block_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOCK_ID);
    }

    public function set_block_id($block_id)
    {
        $this->set_default_property(self :: PROPERTY_BLOCK_ID, $block_id);
    }

    public function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    public function set_variable($variable)
    {
        $this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
    }

    public function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    public function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }
}
