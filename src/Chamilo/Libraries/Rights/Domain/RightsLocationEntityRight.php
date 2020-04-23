<?php
namespace Chamilo\Libraries\Rights\Domain;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Libraries\Rights\Domain
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocationEntityRight extends DataClass
{
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_LOCATION_ID = 'location_id';
    const PROPERTY_RIGHT_ID = 'right_id';

    /**
     * @param array $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_RIGHT_ID,
                self::PROPERTY_ENTITY_ID,
                self::PROPERTY_ENTITY_TYPE,
                self::PROPERTY_LOCATION_ID
            )
        );
    }

    /**
     * @return integer
     */
    public function get_entity_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    /**
     * @return integer
     */
    public function get_entity_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     * @return integer
     */
    public function get_location_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCATION_ID);
    }

    /**
     * @return integer
     */
    public function get_right_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_RIGHT_ID);
    }

    /**
     * @param integer $entityIdentifier
     *
     * @throws \Exception
     */
    public function set_entity_id($entityIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entityIdentifier);
    }

    /**
     * @param integer $entityType
     *
     * @throws \Exception
     */
    public function set_entity_type($entityType)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * @param integer $locationIdentifier
     *
     * @throws \Exception
     */
    public function set_location_id($locationIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCATION_ID, $locationIdentifier);
    }

    /**
     * @param integer $rightIdentifier
     *
     * @throws \Exception
     */
    public function set_right_id($rightIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_RIGHT_ID, $rightIdentifier);
    }
}
