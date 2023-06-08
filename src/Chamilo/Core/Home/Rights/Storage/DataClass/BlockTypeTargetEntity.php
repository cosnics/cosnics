<?php
namespace Chamilo\Core\Home\Rights\Storage\DataClass;

use Chamilo\Core\Home\Rights\Manager;

/**
 * Defines the target entities for a home block type.
 * When a home block type is connected to target
 * entities it becomes limited for the target entities only when adding new blocks
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeTargetEntity extends HomeTargetEntity
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_BLOCK_TYPE = 'block_type';

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_BLOCK_TYPE]);
    }

    public static function getStorageUnitName(): string
    {
        return 'home_block_type_target_entity';
    }

    public function get_block_type(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_BLOCK_TYPE);
    }

    public function set_block_type(string $block_type): void
    {
        $this->setDefaultProperty(self::PROPERTY_BLOCK_TYPE, $block_type);
    }
}
