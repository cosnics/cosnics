<?php
namespace Chamilo\Core\User\Roles\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines the relation between a user and a role
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RoleRelation extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
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
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_ROLE_ID;
        
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userId);
        
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
        return 'user_role_relation';
    }
}