<?php
namespace Chamilo\Core\Home\Rights\Storage\DataClass;

/**
 * Defines the target entities for a home element instance.
 * When a home element instance is connected to target
 * entities it becomes limited for the target entities only when setting the default homepage
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementTargetEntity extends HomeTargetEntity
{
    const PROPERTY_ELEMENT_ID = 'element_id';

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        return parent::get_default_property_names(array(self::PROPERTY_ELEMENT_ID));
    }

    /**
     *
     * @return integer
     */
    public function get_element_id()
    {
        return $this->get_default_property(self::PROPERTY_ELEMENT_ID);
    }

    /**
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'home_element_type_target_entity';
    }

    /**
     *
     * @param integer $element_id
     */
    public function set_element_id($element_id)
    {
        $this->set_default_property(self::PROPERTY_ELEMENT_ID, $element_id);
    }
}
