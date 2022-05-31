<?php
namespace Chamilo\Core\Rights;

use Chamilo\Core\Rights\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Rights
 *
 * @deprecated Use \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight now
 */
abstract class RightsLocationEntityRight extends DataClass
{
    // Keep track of the context so we know which table to call
    private $context;
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_LOCATION_ID = 'location_id';
    const PROPERTY_RIGHT_ID = 'right_id';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_RIGHT_ID, 
                self::PROPERTY_ENTITY_ID, 
                self::PROPERTY_ENTITY_TYPE, 
                self::PROPERTY_LOCATION_ID));
    }

    public function get_context()
    {
        return $this->context;
    }

    public function set_context($context)
    {
        $this->context = $context;
    }

    public function get_entity_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    public function set_entity_id($entity_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    public function get_entity_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    public function set_entity_type($entity_type)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entity_type);
    }

    public function get_location_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCATION_ID);
    }

    public function set_location_id($location_id)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCATION_ID, $location_id);
    }

    public function get_right_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_RIGHT_ID);
    }

    public function set_right_id($right_id)
    {
        $this->setDefaultProperty(self::PROPERTY_RIGHT_ID, $right_id);
    }
}
