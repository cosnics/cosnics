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
        return $this->get_default_property(self::PROPERTY_ROLE);
    }

    /**
     * Get the default properties of all data classes.
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ROLE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
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
        $this->set_default_property(self::PROPERTY_ROLE, $role);

        return $this;
    }
}