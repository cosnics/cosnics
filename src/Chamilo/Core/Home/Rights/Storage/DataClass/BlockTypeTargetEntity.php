<?php

namespace Chamilo\Core\Home\Rights\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines the target entities for a home block type. When a home block type is connected to target
 * entities it becomes limited for the target entities only when adding new blocks
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeTargetEntity extends DataClass
{
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_BLOCK_TYPE = 'block_type';

    /**
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent:: get_default_property_names(
            array(
                self :: PROPERTY_ENTITY_TYPE,
                self :: PROPERTY_ENTITY_ID,
                self :: PROPERTY_BLOCK_TYPE
            )
        );
    }

    /**
     * @return string
     */
    public function get_entity_type()
    {
        return $this->get_default_property(self :: PROPERTY_ENTITY_TYPE);
    }

    /**
     * @param string $entity_type
     */
    public function set_entity_type($entity_type)
    {
        $this->set_default_property(self :: PROPERTY_ENTITY_TYPE, $entity_type);
    }

    /**
     * @return integer
     */
    public function get_entity_id()
    {
        return $this->get_default_property(self :: PROPERTY_ENTITY_ID);
    }

    /**
     * @param integer $entity_id
     */
    public function set_entity_id($entity_id)
    {
        $this->set_default_property(self :: PROPERTY_ENTITY_ID, $entity_id);
    }

    /**
     * @return string
     */
    public function get_block_type()
    {
        return $this->get_default_property(self :: PROPERTY_BLOCK_TYPE);
    }

    /**
     * @param string $block_type
     */
    public function set_block_type($block_type)
    {
        $this->set_default_property(self :: PROPERTY_BLOCK_TYPE, $block_type);
    }
}
