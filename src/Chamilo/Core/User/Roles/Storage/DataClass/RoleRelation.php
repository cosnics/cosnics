<?php
namespace Chamilo\Core\User\Roles\Storage\DataClass;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines the relation between a user and a role
 *
 * @package Chamilo\Core\User\Roles\Storage\DataClass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class RoleRelation extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ROLE_ID = 'role_id';
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_ROLE_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function getRoleId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_ROLE_ID);
    }

    public static function getStorageUnitName(): string
    {
        return 'user_role_relation';
    }

    public function getUserId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function setRoleId(string $roleId): RoleRelation
    {
        $this->setDefaultProperty(self::PROPERTY_ROLE_ID, $roleId);

        return $this;
    }

    public function setUserId(string $userId): RoleRelation
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);

        return $this;
    }
}