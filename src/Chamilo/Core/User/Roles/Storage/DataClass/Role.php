<?php
namespace Chamilo\Core\User\Roles\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines a role
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Role extends DataClass
{
    const PROPERTY_ROLE = 'role';

    /**
     *
     * @return string
     */
    public function getRole()
    {
        return $this->getDefaultProperty(self::PROPERTY_ROLE);
    }

    /**
     * Get the default properties of all data classes.
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ROLE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'user_role';
    }

    /**
     *
     * @param string $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        $this->setDefaultProperty(self::PROPERTY_ROLE, $role);

        return $this;
    }
}