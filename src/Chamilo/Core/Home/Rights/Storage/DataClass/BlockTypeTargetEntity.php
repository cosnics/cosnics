<?php
namespace Chamilo\Core\Home\Rights\Storage\DataClass;

/**
 * Defines the target entities for a home block type.
 * When a home block type is connected to target
 * entities it becomes limited for the target entities only when adding new blocks
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeTargetEntity extends HomeTargetEntity
{
    public const PROPERTY_BLOCK_TYPE = 'block_type';

    /**
     *
     * @return string
     */
    public function get_block_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_BLOCK_TYPE);
    }

    /**
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_BLOCK_TYPE));
    }

    /**
     *
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'home_block_type_target_entity';
    }

    /**
     *
     * @param string $block_type
     */
    public function set_block_type($block_type)
    {
        $this->setDefaultProperty(self::PROPERTY_BLOCK_TYPE, $block_type);
    }
}
