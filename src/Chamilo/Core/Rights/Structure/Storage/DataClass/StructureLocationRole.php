<?php
namespace Chamilo\Core\Rights\Structure\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines the relation between a structure location and a role
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocationRole extends DataClass
{
    const PROPERTY_STRUCTURE_LOCATION_ID = 'structure_location_id';
    const PROPERTY_ROLE_ID = 'role_id';

    /**
     * Get the default properties of all data classes.
     * 
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_STRUCTURE_LOCATION_ID;
        $extendedPropertyNames[] = self::PROPERTY_ROLE_ID;
        
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     *
     * @return int
     */
    public function getStructureLocationId()
    {
        return $this->get_default_property(self::PROPERTY_STRUCTURE_LOCATION_ID);
    }

    /**
     *
     * @param int $structureLocationId
     *
     * @return $this
     */
    public function setStructureLocationId($structureLocationId)
    {
        $this->set_default_property(self::PROPERTY_STRUCTURE_LOCATION_ID, $structureLocationId);
        
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getRoleId()
    {
        return $this->get_default_property(self::PROPERTY_ROLE_ID);
    }

    /**
     *
     * @param int $roleId
     *
     * @return $this
     */
    public function setRoleId($roleId)
    {
        $this->set_default_property(self::PROPERTY_ROLE_ID, $roleId);
        
        return $this;
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'rights_structure_location_role';
    }
}