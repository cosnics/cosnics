<?php
namespace Chamilo\Core\User\Roles\Storage\DataClass;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines a role
 *
 * @package Chamilo\Core\User\Roles\Storage\DataClass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class Role extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ROLE = 'role';

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ROLE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function getRole(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_ROLE);
    }

    public static function getStorageUnitName(): string
    {
        return 'user_role';
    }

    public function setRole(string $role): Role
    {
        $this->setDefaultProperty(self::PROPERTY_ROLE, $role);

        return $this;
    }
}