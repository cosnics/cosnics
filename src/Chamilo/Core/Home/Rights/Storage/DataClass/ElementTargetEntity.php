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
    public const PROPERTY_ELEMENT_ID = 'element_id';

    /**
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_ELEMENT_ID));
    }

    /**
     *
     * @return int
     */
    public function get_element_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ELEMENT_ID);
    }

    /**
     *
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'home_element_type_target_entity';
    }

    /**
     *
     * @param int $element_id
     */
    public function set_element_id($element_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ELEMENT_ID, $element_id);
    }
}
