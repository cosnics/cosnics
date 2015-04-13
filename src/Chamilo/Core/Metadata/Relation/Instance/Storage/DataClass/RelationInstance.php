<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Ehb\Core\Metadata\Relation\Instance\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RelationInstance extends DataClass
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_SOURCE_TYPE = 'source_type';
    const PROPERTY_SOURCE_ID = 'source_id';
    const PROPERTY_TARGET_TYPE = 'target_type';
    const PROPERTY_TARGET_ID = 'target_id';
    const PROPERTY_RELATION_ID = 'relation_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CREATION_DATE = 'creation_date';

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */

    /**
     * Get the default properties
     *
     * @param string[] $extended_property_names
     *
     * @return string[] The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_ENTITY_TYPE;
        $extended_property_names[] = self :: PROPERTY_ENTITY_ID;
        $extended_property_names[] = self :: PROPERTY_SCHEMA_ID;
        $extended_property_names[] = self :: PROPERTY_USER_ID;
        $extended_property_names[] = self :: PROPERTY_CREATION_DATE;

        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     *
     * @return string
     */
    public function get_entity_type()
    {
        return $this->get_default_property(self :: PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param string $entityType
     */
    public function set_entity_type($entityType)
    {
        $this->set_default_property(self :: PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     *
     * @return string
     */
    public function get_entity_id()
    {
        return $this->get_default_property(self :: PROPERTY_ENTITY_ID);
    }

    /**
     *
     * @param string $entityId
     */
    public function set_entity_id($entityId)
    {
        $this->set_default_property(self :: PROPERTY_ENTITY_ID, $entityId);
    }

    /**
     *
     * @return int
     */
    public function get_schema_id()
    {
        return $this->get_default_property(self :: PROPERTY_SCHEMA_ID);
    }

    /**
     *
     * @param int $schemaId
     */
    public function set_schema_id($schemaId)
    {
        $this->set_default_property(self :: PROPERTY_SCHEMA_ID, $schemaId);
    }

    /**
     *
     * @return integer
     */
    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     *
     * @param integer
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    public function getUser()
    {
        return DataManager :: retrieve_by_id(User :: class_name(), $this->get_user_id());
    }

    /**
     *
     * @return integer
     */
    public function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }

    /**
     *
     * @param integer
     */
    public function set_creation_date($creationDate)
    {
        $this->set_default_property(self :: PROPERTY_CREATION_DATE, $creationDate);
    }
}