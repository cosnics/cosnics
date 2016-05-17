<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 * Class that represents a condition property
 * Used in action bar renderer to translate these condition property's to actual PatternMatchConditions
 * 
 * @author Sven Vanpoucke
 */
class ConditionProperty
{

    private $property;

    private $storage_unit;

    private $is_alias_storage_unit;

    public function __construct($property, $storage_unit = null, $is_alias_storage_unit = false)
    {
        $this->set_property($property);
        $this->set_storage_unit($storage_unit);
        $this->set_is_alias_storage_unit($is_alias_storage_unit);
    }

    /**
     *
     * @return the $property
     */
    public function get_property()
    {
        return $this->property;
    }

    /**
     *
     * @return the $storage_unit
     */
    public function get_storage_unit()
    {
        return $this->storage_unit;
    }

    /**
     *
     * @param $property the $property to set
     */
    public function set_property($property)
    {
        $this->property = $property;
    }

    /**
     *
     * @param $storage_unit the $storage_unit to set
     */
    public function set_storage_unit($storage_unit)
    {
        $this->storage_unit = $storage_unit;
    }

    /**
     *
     * @return the $is_alias_storage_unit
     */
    public function get_is_alias_storage_unit()
    {
        return $this->is_alias_storage_unit;
    }

    /**
     *
     * @param $is_alias_storage_unit the $is_alias_storage_unit to set
     */
    public function set_is_alias_storage_unit($is_alias_storage_unit)
    {
        $this->is_alias_storage_unit = $is_alias_storage_unit;
    }
}
