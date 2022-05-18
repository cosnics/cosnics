<?php
namespace Chamilo\Core\Home\Rights\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines the target entities for usage in home elements / block types ...
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class HomeTargetEntity extends DataClass
{
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';

    /**
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_ID;
        
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     *
     * @return string
     */
    public function get_entity_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param string $entity_type
     */
    public function set_entity_type($entity_type)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entity_type);
    }

    /**
     *
     * @return integer
     */
    public function get_entity_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    /**
     *
     * @param integer $entity_id
     */
    public function set_entity_id($entity_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entity_id);
    }
}
