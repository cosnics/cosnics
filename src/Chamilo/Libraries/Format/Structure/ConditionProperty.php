<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 * Class that represents a condition property
 * Used in action bar renderer to translate these condition property's to actual PatternMatchConditions
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Sven Vanpoucke
 */
class ConditionProperty
{

    /**
     *
     * @var string
     */
    private $property;

    /**
     *
     * @var string
     */
    private $storage_unit;

    /**
     *
     * @var boolean
     */
    private $is_alias_storage_unit;

    /**
     *
     * @param string $property
     * @param string $storageUnit
     * @param boolean $isAliasStorageUnit
     */
    public function __construct($property, $storageUnit = null, $isAliasStorageUnit = false)
    {
        $this->set_property($property);
        $this->set_storage_unit($storageUnit);
        $this->set_is_alias_storage_unit($isAliasStorageUnit);
    }

    /**
     *
     * @return string
     */
    public function get_property()
    {
        return $this->property;
    }

    /**
     *
     * @return string
     */
    public function get_storage_unit()
    {
        return $this->storage_unit;
    }

    /**
     *
     * @param string $property
     */
    public function set_property($property)
    {
        $this->property = $property;
    }

    /**
     *
     * @param string $storageUnit
     */
    public function set_storage_unit($storageUnit)
    {
        $this->storage_unit = $storageUnit;
    }

    /**
     *
     * @return boolean
     */
    public function get_is_alias_storage_unit()
    {
        return $this->is_alias_storage_unit;
    }

    /**
     *
     * @param boolean $isAliasStorageUnit
     */
    public function set_is_alias_storage_unit($isAliasStorageUnit)
    {
        $this->is_alias_storage_unit = $isAliasStorageUnit;
    }
}
