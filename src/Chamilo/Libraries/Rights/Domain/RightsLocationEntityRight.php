<?php
namespace Chamilo\Libraries\Rights\Domain;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Libraries\Rights\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class RightsLocationEntityRight extends DataClass
{
    public const CONTEXT = 'Chamilo\Libraries\Rights';

    public const PROPERTY_ENTITY_ID = 'entity_id';
    public const PROPERTY_ENTITY_TYPE = 'entity_type';
    public const PROPERTY_LOCATION_ID = 'location_id';
    public const PROPERTY_RIGHT_ID = 'right_id';

    /**
     * @param array $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_RIGHT_ID,
                self::PROPERTY_ENTITY_ID,
                self::PROPERTY_ENTITY_TYPE,
                self::PROPERTY_LOCATION_ID
            ]
        );
    }

    /**
     * @return int
     */
    public function get_entity_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    /**
     * @return int
     */
    public function get_entity_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     * @return int
     */
    public function get_location_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCATION_ID);
    }

    /**
     * @return int
     */
    public function get_right_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_RIGHT_ID);
    }

    /**
     * @param int $entityIdentifier
     *
     * @throws \Exception
     */
    public function set_entity_id($entityIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entityIdentifier);
    }

    /**
     * @param int $entityType
     *
     * @throws \Exception
     */
    public function set_entity_type($entityType)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * @param int $locationIdentifier
     *
     * @throws \Exception
     */
    public function set_location_id($locationIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCATION_ID, $locationIdentifier);
    }

    /**
     * @param int $rightIdentifier
     *
     * @throws \Exception
     */
    public function set_right_id($rightIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_RIGHT_ID, $rightIdentifier);
    }
}
