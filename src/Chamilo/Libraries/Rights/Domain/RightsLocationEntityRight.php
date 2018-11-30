<?php
namespace Chamilo\Libraries\Rights\Domain;

use Chamilo\Core\Rights\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Libraries\Rights\Domain
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocationEntityRight extends DataClass
{
    // Keep track of the context so we know which table to call
    const PROPERTY_ENTITY_ID = 'entity_id';

    const PROPERTY_ENTITY_TYPE = 'entity_type';

    const PROPERTY_LOCATION_ID = 'location_id';

    const PROPERTY_RIGHT_ID = 'right_id';

    private $context;

    public function get_context()
    {
        return $this->context;
    }

    public function set_context($context)
    {
        $this->context = $context;
    }

    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_RIGHT_ID, self::PROPERTY_ENTITY_ID, self::PROPERTY_ENTITY_TYPE,
                self::PROPERTY_LOCATION_ID
            )
        );
    }

    public function get_entity_id()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_ID);
    }

    public function get_entity_type()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    public function get_location_id()
    {
        return $this->get_default_property(self::PROPERTY_LOCATION_ID);
    }

    public function get_right_id()
    {
        return $this->get_default_property(self::PROPERTY_RIGHT_ID);
    }

    public function set_entity_id($entity_id)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    public function set_entity_type($entity_type)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entity_type);
    }

    public function set_location_id($location_id)
    {
        $this->set_default_property(self::PROPERTY_LOCATION_ID, $location_id);
    }

    public function set_right_id($right_id)
    {
        $this->set_default_property(self::PROPERTY_RIGHT_ID, $right_id);
    }
}
