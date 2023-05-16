<?php
namespace Chamilo\Core\Rights\Structure\Storage\DataClass;

use Chamilo\Core\Rights\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines the relation between a structure location and a role
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocationRole extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ROLE_ID = 'role_id';
    public const PROPERTY_STRUCTURE_LOCATION_ID = 'structure_location_id';

    /**
     * Get the default properties of all data classes.
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_STRUCTURE_LOCATION_ID;
        $extendedPropertyNames[] = self::PROPERTY_ROLE_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return int
     */
    public function getRoleId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ROLE_ID);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'rights_structure_location_role';
    }

    /**
     * @return int
     */
    public function getStructureLocationId()
    {
        return $this->getDefaultProperty(self::PROPERTY_STRUCTURE_LOCATION_ID);
    }

    /**
     * @param int $roleId
     *
     * @return $this
     */
    public function setRoleId($roleId)
    {
        $this->setDefaultProperty(self::PROPERTY_ROLE_ID, $roleId);

        return $this;
    }

    /**
     * @param int $structureLocationId
     *
     * @return $this
     */
    public function setStructureLocationId($structureLocationId)
    {
        $this->setDefaultProperty(self::PROPERTY_STRUCTURE_LOCATION_ID, $structureLocationId);

        return $this;
    }
}