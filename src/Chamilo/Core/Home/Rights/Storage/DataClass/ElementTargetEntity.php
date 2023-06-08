<?php
namespace Chamilo\Core\Home\Rights\Storage\DataClass;

use Chamilo\Core\Home\Rights\Manager;

/**
 * Defines the target entities for a home element instance.
 * When a home element instance is connected to target
 * entities it becomes limited for the target entities only when setting the default homepage
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementTargetEntity extends HomeTargetEntity
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ELEMENT_ID = 'element_id';

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_ELEMENT_ID]);
    }

    public static function getStorageUnitName(): string
    {
        return 'home_element_type_target_entity';
    }

    public function get_element_id(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_ELEMENT_ID);
    }

    public function set_element_id(string $element_id): void
    {
        $this->setDefaultProperty(self::PROPERTY_ELEMENT_ID, $element_id);
    }
}
