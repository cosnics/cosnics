<?php
namespace Chamilo\Core\Home\Rights\Storage\DataClass;

use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines the target entities for usage in home elements / block types ...
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class HomeTargetEntity extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ENTITY_ID = 'entity_id';
    public const PROPERTY_ENTITY_TYPE = 'entity_type';

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function get_entity_id(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    public function get_entity_type(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    public function set_entity_id(string $entity_id): void
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    public function set_entity_type(string $entity_type): void
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entity_type);
    }
}
